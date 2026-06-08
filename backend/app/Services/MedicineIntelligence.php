<?php
namespace App\Services;

use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DrugInteraction;
use App\Models\HealthMetric;

/**
 * Medicine Intelligence Service — Clinical Recommendation Engine
 */
class MedicineIntelligence {

    private $medicineModel;
    private $orderModel;
    private $interactionModel;
    private $metricModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
        $this->orderModel = new Order();
        $this->interactionModel = new DrugInteraction();
        $this->metricModel = new HealthMetric();
    }

    /**
     * Check interactions between a primary medicine and a list of existing medicines
     */
    public function checkInteractions($primaryId, $existingIds) {
        $warnings = [];
        $highestSeverity = 'safe';

        foreach ($existingIds as $id) {
            $interaction = $this->interactionModel->findInteraction($primaryId, $id);
            if ($interaction) {
                $warnings[] = [
                    'medicine_id' => $id,
                    'severity' => $interaction['severity'],
                    'message' => $interaction['warning_text']
                ];
                
                // Track highest severity
                if ($interaction['severity'] === 'severe') $highestSeverity = 'severe';
                elseif ($interaction['severity'] === 'moderate' && $highestSeverity !== 'severe') $highestSeverity = 'moderate';
                elseif ($interaction['severity'] === 'low' && $highestSeverity === 'safe') $highestSeverity = 'low';
            }
        }

        return [
            'status' => $highestSeverity,
            'warnings' => $warnings,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Analyze health metrics to provide smart clinical suggestions
     */
    public function analyzeHealthData($userId) {
        $metrics = $this->metricModel->getLatestByUser($userId, 5);
        $alerts = [];

        foreach ($metrics as $m) {
            if ($m['metric_type'] === 'glucose' && $m['value_1'] > 200) {
                $alerts[] = "High Glucose Reading Detected (" . $m['value_1'] . "). Consider Diabetic Consultation.";
            }
            if ($m['metric_type'] === 'blood_pressure' && $m['value_1'] > 140) {
                $alerts[] = "Hypertension Risk: High Systolic BP detected (" . $m['value_1'] . ").";
            }
        }

        return [
            'insights' => $alerts,
            'metric_count' => count($metrics)
        ];
    }

    /**
     * Generate clinical recommendations for a user
     * Improved logic: order history + same salt + same category + frequently bought together + popularity
     */
    public function getRecommendations($userId) {
        $db = \App\Core\Database::getInstance()->getConnection();

        // 1. Get user's purchase history (medicines they've already bought)
        $historyStmt = $db->prepare("
            SELECT DISTINCT oi.medicine_id, m.category_id, m.salt, m.brand_id
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN medicines m ON oi.medicine_id = m.id
            WHERE o.user_id = ? AND o.status IN ('delivered', 'confirmed', 'dispatched')
            ORDER BY o.created_at DESC
            LIMIT 50
        ");
        $historyStmt->execute([$userId]);
        $purchasedItems = $historyStmt->fetchAll(\PDO::FETCH_ASSOC);

        $purchasedIds = array_column($purchasedItems, 'medicine_id');
        $purchasedCategories = array_filter(array_column($purchasedItems, 'category_id'));
        $purchasedSalts = array_filter(array_column($purchasedItems, 'salt'));

        $recommendations = [];
        $primaryFocus = "General Wellness";

        // 2. Same Salt recommendations (generic alternatives - highest priority)
        if (!empty($purchasedSalts)) {
            $uniqueSalts = array_unique($purchasedSalts);
            $placeholders = implode(',', array_fill(0, count($uniqueSalts), '?'));
            $excludePlaceholders = !empty($purchasedIds) ? ' AND id NOT IN (' . implode(',', array_fill(0, count($purchasedIds), '?')) . ')' : '';
            
            $saltStmt = $db->prepare("
                SELECT *, 'same_salt' as reason, 95 as score
                FROM medicines 
                WHERE salt IN ($placeholders) 
                  AND approval_status = 'approved' AND status = 1
                  $excludePlaceholders
                ORDER BY price ASC
                LIMIT 4
            ");
            $params = array_merge(array_values($uniqueSalts), $purchasedIds);
            $saltStmt->execute($params);
            $saltRecs = $saltStmt->fetchAll(\PDO::FETCH_ASSOC);
            $recommendations = array_merge($recommendations, $saltRecs);
        }

        // 3. Same Category recommendations (related products)
        if (!empty($purchasedCategories)) {
            $catCounts = array_count_values($purchasedCategories);
            arsort($catCounts);
            $topCatId = array_key_first($catCounts);
            
            $categoryModel = new \App\Models\Category();
            $cat = $categoryModel->findById($topCatId);
            if ($cat) $primaryFocus = $cat['name'];

            $existingIds = array_merge($purchasedIds, array_column($recommendations, 'id'));
            $excludePlaceholders = !empty($existingIds) ? ' AND id NOT IN (' . implode(',', array_fill(0, count($existingIds), '?')) . ')' : '';

            $catStmt = $db->prepare("
                SELECT *, 'same_category' as reason, 80 as score
                FROM medicines 
                WHERE category_id = ? 
                  AND approval_status = 'approved' AND status = 1
                  $excludePlaceholders
                ORDER BY RAND()
                LIMIT 4
            ");
            $params = array_merge([$topCatId], $existingIds);
            $catStmt->execute($params);
            $catRecs = $catStmt->fetchAll(\PDO::FETCH_ASSOC);
            $recommendations = array_merge($recommendations, $catRecs);
        }

        // 4. Frequently Bought Together (users who bought X also bought Y)
        if (!empty($purchasedIds)) {
            $recentId = $purchasedIds[0];
            $fbtStmt = $db->prepare("
                SELECT m.*, 'frequently_bought_together' as reason, 85 as score
                FROM order_items oi2
                JOIN medicines m ON oi2.medicine_id = m.id
                WHERE oi2.order_id IN (
                    SELECT DISTINCT oi.order_id FROM order_items oi WHERE oi.medicine_id = ?
                )
                AND oi2.medicine_id != ?
                AND oi2.medicine_id NOT IN (" . implode(',', array_fill(0, max(1, count($purchasedIds)), '?')) . ")
                AND m.approval_status = 'approved' AND m.status = 1
                GROUP BY oi2.medicine_id
                ORDER BY COUNT(*) DESC
                LIMIT 3
            ");
            $params = array_merge([$recentId, $recentId], $purchasedIds);
            $fbtStmt->execute($params);
            $fbtRecs = $fbtStmt->fetchAll(\PDO::FETCH_ASSOC);
            $recommendations = array_merge($recommendations, $fbtRecs);
        }

        // 5. Popular/Trending medicines (fallback + fill)
        $existingIds = array_merge($purchasedIds, array_column($recommendations, 'id'));
        $excludePlaceholders = !empty($existingIds) ? ' WHERE id NOT IN (' . implode(',', array_fill(0, count($existingIds), '?')) . ') AND' : ' WHERE';

        $popularStmt = $db->prepare("
            SELECT m.*, 'trending' as reason, 60 as score
            FROM medicines m
            $excludePlaceholders m.approval_status = 'approved' AND m.status = 1
            ORDER BY m.created_at DESC
            LIMIT 4
        ");
        $popularStmt->execute($existingIds);
        $popularRecs = $popularStmt->fetchAll(\PDO::FETCH_ASSOC);
        $recommendations = array_merge($recommendations, $popularRecs);

        // 6. Deduplicate and sort by score
        $seen = [];
        $unique = [];
        foreach ($recommendations as $rec) {
            if (!in_array($rec['id'], $seen)) {
                $seen[] = $rec['id'];
                $unique[] = $rec;
            }
        }
        usort($unique, fn($a, $b) => ($b['score'] ?? 0) - ($a['score'] ?? 0));

        // Build insight summary
        $insightParts = [];
        if (!empty($purchasedSalts)) $insightParts[] = "generic alternatives for your medications";
        if (!empty($purchasedCategories)) $insightParts[] = "products in your preferred category ($primaryFocus)";
        $insightParts[] = "trending items on MediMitra";
        $insightSummary = "Personalized picks based on: " . implode(', ', $insightParts) . ".";

        return [
            'recommendations' => array_values(array_slice($unique, 0, 12)),
            'primary_focus' => $primaryFocus,
            'insight_summary' => $insightSummary,
            'algorithm' => 'hybrid_v2',
            'factors' => ['order_history', 'salt_matching', 'category_affinity', 'frequently_bought_together', 'trending']
        ];
    }

    /**
     * Get substitutes for a medicine
     */
    public function getSubstitutes($id) {
        return $this->medicineModel->getSubstitutes($id);
    }

    /**
     * Finds medicines in a block of text using improved fuzzy matching
     * Supports: partial name match, salt/generic match, multi-word fuzzy, dosage extraction
     * @param string $text
     * @return array
     */
    public function findMedicinesInText($text) {
        if (empty($text)) return [];
        
        $allMeds = $this->medicineModel->findAll();
        $text = strtolower(trim($text));
        $found = [];
        $foundIds = [];
        $matchedCoreNames = []; // Prevent duplicate variants (e.g., paracetamol 500 + paracetamol 650)

        // Stop words for single-word matching only (not for multi-word/full name)
        $singleWordStops = ['digital', 'product', 'category', 'pharmaceutical', 'payment', 'receipt', 
                      'transaction', 'delivery', 'settled', 'authorized', 'subtotal', 'total',
                      'clinical', 'source', 'sector', 'patient', 'ship', 'cost', 'standard',
                      'general', 'hospital', 'clinic', 'doctor', 'medical', 'health',
                      'antibiotic', 'supplement', 'tablet', 'tablets', 'capsule', 'capsules', 'syrup', 'syrups',
                      'injection', 'injections', 'ointment', 'ointments', 'cream', 'creams', 'drops', 'suspension',
                      'suspensions', 'pediatric', 'network', 'pharmacy', 'plaza', 'mumbai', 'maharashtra', 'phaltan', 'shop',
                      'generic', 'solution', 'solutions'];

        foreach ($allMeds as $med) {
            if (in_array($med['id'], $foundIds)) continue;

            $medName = strtolower(trim($med['name']));
            $medSalt = strtolower(trim($med['salt'] ?? ''));

            // Extract core name for dedup (e.g., "paracetamol" from "Paracetamol 500mg")
            $coreName = trim(preg_replace('/\d+\s*(mg|ml|mcg|g|iu|%)/i', '', $medName));
            $coreName = trim(preg_replace('/\b(tablets?|capsules?|syrup|injection|ointment|cream|drops|suspension|gel|powder|solution)\b/i', '', $coreName));
            $coreName = trim(preg_replace('/\s+/', ' ', $coreName));

            // Skip if same core already matched (prevents "paracetamol 650" when "paracetamol 500mg" already found)
            if (in_array($coreName, $matchedCoreNames)) continue;
            
            // 1. Full medicine name exact match in text
            if (strpos($text, $medName) !== false) {
                $found[] = $med;
                $foundIds[] = $med['id'];
                $matchedCoreNames[] = $coreName;
                continue;
            }

            // 2. Name without dosage match (e.g., "amoxicillin capsules" without "250mg")
            $nameNoDosage = trim(preg_replace('/\d+\s*(mg|ml|mcg|g|iu|%)/i', '', $medName));
            if (strlen($nameNoDosage) >= 6 && strpos($text, $nameNoDosage) !== false) {
                $found[] = $med;
                $foundIds[] = $med['id'];
                $matchedCoreNames[] = $coreName;
                continue;
            }

            // 3. Core identity match
            if (strlen($coreName) >= 5 && strpos($text, $coreName) !== false) {
                $found[] = $med;
                $foundIds[] = $med['id'];
                $matchedCoreNames[] = $coreName;
                continue;
            }

            // 4. Single specific word match (must be 8+ chars to be truly unique)
            $nameParts = preg_split('/\s+/', $coreName);
            foreach ($nameParts as $part) {
                $part = trim($part);
                $partClean = preg_replace('/[^a-z0-9]/', '', $part);
                if (strlen($partClean) >= 8 && !in_array($partClean, $singleWordStops) && strpos($text, $partClean) !== false) {
                    $found[] = $med;
                    $foundIds[] = $med['id'];
                    $matchedCoreNames[] = $coreName;
                    break;
                }
            }
            if (in_array($med['id'], $foundIds)) continue;

            // 5. Salt/generic name match (8+ chars, specific)
            if (!empty($medSalt)) {
                $saltCore = trim(preg_replace('/\d+\s*(mg|ml|mcg|g|iu|%)/i', '', $medSalt));
                $saltCoreClean = preg_replace('/[^a-z0-9]/', '', $saltCore);
                if (strlen($saltCoreClean) >= 8 && !in_array($saltCoreClean, $singleWordStops) && strpos($text, $saltCoreClean) !== false) {
                    $found[] = $med;
                    $foundIds[] = $med['id'];
                    $matchedCoreNames[] = $coreName;
                    continue;
                }
                $saltParts = preg_split('/[\s,\+\/]+/', $saltCore);
                foreach ($saltParts as $sp) {
                    $sp = trim($sp);
                    $spClean = preg_replace('/[^a-z0-9]/', '', $sp);
                    if (strlen($spClean) >= 8 && !in_array($spClean, $singleWordStops) && strpos($text, $spClean) !== false) {
                        $found[] = $med;
                        $foundIds[] = $med['id'];
                        $matchedCoreNames[] = $coreName;
                        break;
                    }
                }
            }
        }

        return $found;
    }
}
