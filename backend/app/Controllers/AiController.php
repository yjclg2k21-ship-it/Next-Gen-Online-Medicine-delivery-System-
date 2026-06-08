<?php
namespace App\Controllers;

use App\Services\MedicineIntelligence;
use App\Services\PrescriptionProcessor;
use App\Core\Database;
use App\Core\ResponseHandler;

/**
 * AI Controller
 * Exposes internal AI service interfaces (salt matching, OCR) to the React frontend.
 */
class AiController extends BaseController {

    private $medIntelligence;
    private $ocrProcessor;
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->medIntelligence = new MedicineIntelligence();
        $this->ocrProcessor = new PrescriptionProcessor();
    }

    public function findSubstitutes() {
        $medicineId = $_GET['medicine_id'] ?? 0;
        
        $substitutes = $this->medIntelligence->getSubstitutes($medicineId);
        return $this->json(['success' => true, 'substitutes' => $substitutes]);
    }

    public function checkInteractions() {
        $data = $this->getPostData();
        $medicineIds = $data['medicines'] ?? [];
        
        // Pass the first ID as primary and the rest as existing for interaction analysis
        if (empty($medicineIds)) return $this->json(['status' => 'safe', 'warnings' => []]);
        
        $primary = array_shift($medicineIds);
        $analysis = $this->medIntelligence->checkInteractions($primary, $medicineIds);
        return $this->json($analysis);
    }

    /**
     * Analyze Prescription (OCR Simulation)
     * Maps to the 'Digital Healthcare Sync' feature in the React frontend.
     */
    public function analyzePrescription() {
        $data = $this->getPostData();
        $text = $data['text'] ?? '';
        
        if (empty($text)) {
            return $this->json(['success' => false, 'message' => 'No clinical text provided for analysis.']);
        }

        // Try ML service first (Python microservice on port 5050)
        $mlResult = $this->callMlService($text);
        
        if ($mlResult && !empty($mlResult['medicines'])) {
            // ML service responded with matches
            $mapped = array_map(function($m) {
                return [
                    'id' => $m['id'] ?? $m['medicine_id'],
                    'medicine_id' => $m['id'] ?? $m['medicine_id'],
                    'name' => $m['name'],
                    'price' => $m['price'] ?? 0,
                    'confidence' => $m['confidence'] ?? 90,
                    'dosage' => 'As per prescription'
                ];
            }, $mlResult['medicines']);

            return $this->json([
                'success' => true,
                'medicines' => $mapped,
                'method' => 'ml_model',
                'analysis_id' => $mlResult['analysis_id'] ?? 'ML-' . strtoupper(uniqid())
            ]);
        }

        // Fallback: Rule-based matching
        $medicines = $this->medIntelligence->findMedicinesInText($text);
        
        // Deduplicate by medicine name
        $seen = [];
        $unique = [];
        foreach ($medicines as $m) {
            $key = strtolower(trim($m['name']));
            if (!in_array($key, $seen)) {
                $seen[] = $key;
                $unique[] = $m;
            }
        }

        $mapped = array_map(function($m) {
            return [
                'id' => $m['id'],
                'medicine_id' => $m['id'],
                'name' => $m['name'],
                'price' => $m['price'] ?? 0,
                'dosage' => 'As per prescription',
                'confidence' => 85
            ];
        }, $unique);

        return $this->json([
            'success' => true,
            'medicines' => array_values($mapped),
            'method' => 'rule_based',
            'analysis_id' => 'RB-' . strtoupper(uniqid())
        ]);
    }

    /**
     * Call Python ML microservice for prescription analysis
     */
    private function callMlService(string $text): ?array {
        try {
            $ch = curl_init('http://localhost:5050/analyze');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode(['text' => $text]),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 2
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Debug log
            error_log("ML Service: HTTP {$httpCode}, Error: {$curlError}, Response length: " . strlen($response ?: ''));
            
            if ($httpCode === 200 && $response) {
                $result = json_decode($response, true);
                if ($result && $result['success'] && !empty($result['medicines'])) {
                    return $result;
                }
            }
        } catch (\Exception $e) {
            error_log("ML Service Exception: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Find Cheaper Generic Alternatives
     * Logic: Match by 'salt' but filter for lower price
     */
    public function findGenericAlternatives() {
        $medicineId = $_GET['medicine_id'] ?? 0;
        
        try {
            // Get original medicine salt
            $stmt = $this->db->prepare("SELECT salt, price FROM medicines WHERE id = ?");
            $stmt->execute([$medicineId]);
            $original = $stmt->fetch();

            if (!$original || empty($original['salt'])) {
                return ResponseHandler::success(['alternatives' => []]);
            }

            // Extract main active ingredient (first word or before numbers/mg) to broaden search
            $baseSalt = explode(' ', $original['salt'])[0]; 
            $baseSalt = preg_replace('/[0-9]+mg/i', '', $baseSalt);

            // Find others with similar salt but lower price
            $stmt = $this->db->prepare("
                SELECT m.id, m.name, m.price, m.image, b.name as brand_name,
                       ROUND(((? - m.price) / NULLIF(?, 0)) * 100, 1) as savings_percentage
                FROM medicines m 
                LEFT JOIN brands b ON m.brand_id = b.id
                WHERE m.salt LIKE ? 
                AND m.price < ? 
                AND m.id != ? 
                AND m.approval_status = 'approved'
                ORDER BY m.price ASC LIMIT 5
            ");
            
            $searchSalt = '%' . trim($baseSalt) . '%';
            $stmt->execute([$original['price'], $original['price'], $searchSalt, $original['price'], $medicineId]);
            $alternatives = $stmt->fetchAll();

            return ResponseHandler::success(['alternatives' => $alternatives]);
        } catch (\Exception $e) { 
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * Compare two medicines side-by-side
     */
    public function compare() {
        $id1 = $_GET['id1'] ?? null;
        $id2 = $_GET['id2'] ?? null;

        if (!$id1 || !$id2) {
            return ResponseHandler::badRequest("Two medicine IDs are required for comparison.");
        }

        try {
            $stmt = $this->db->prepare("
                SELECT m.id, m.name, m.salt, m.price, m.stock, b.name as brand_name, b.type as brand_type, c.name as category_name
                FROM medicines m
                LEFT JOIN brands b ON m.brand_id = b.id
                LEFT JOIN categories c ON m.category_id = c.id
                WHERE m.id IN (?, ?)
            ");
            $stmt->execute([$id1, $id2]);
            $medicines = $stmt->fetchAll();

            if (count($medicines) !== 2) {
                return ResponseHandler::error("One or both medicines not found.", 404);
            }

            // Organize by ID so frontend can map easily
            $result = [
                $medicines[0]['id'] => $medicines[0],
                $medicines[1]['id'] => $medicines[1]
            ];

            return ResponseHandler::success(['comparison' => $result], 'Comparison generated.');
        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
