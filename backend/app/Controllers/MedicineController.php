<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\Medicine;
use App\Models\Category;
use Exception;

/**
 * Medicine Controller — Aligned with Unified Model Architecture
 */
class MedicineController extends BaseController {
    
    private $medicineModel;
    private $categoryModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
        $this->categoryModel = new Category();
    }

    /**
     * GET /medicines
     */
    public function index() {
        try {
            $medicines = $this->medicineModel->findAll();
            // Filter only approved medicines if not admin
            $medicines = array_filter($medicines, fn($m) => $m['approval_status'] === 'approved');

            // Procedural metric injection for E-Commerce simulation
            foreach ($medicines as &$med) {
                $uid = crc32($med['id'] . 'salt123'); 
                $med['distance'] = round((($uid % 50) + 5) / 10, 1); 
                
                // Use real rating if available, fallback to mock
                if (!empty($med['avg_rating'])) {
                    $med['rating'] = round((float)$med['avg_rating'], 1);
                    $med['reviews'] = (int)$med['total_reviews'];
                } else {
                    $med['rating'] = round((($uid % 9) + 41) / 10, 1); 
                    $med['reviews'] = ($uid % 300) + 24; 
                }

                $med['stock_scarcity'] = (($uid % 10) > 6) ? (($uid % 5) + 1) : false; 
                $med['generic_discount'] = (($uid % 10) < 4) ? (($uid % 4) + 2) * 10 : false; 
            }

            return ResponseHandler::success(['medicines' => array_values($medicines)]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /medicines/{id}
     */
    public function show($id) {
        try {
            $medicine = $this->medicineModel->findById($id);
            if (!$medicine) return ResponseHandler::notFound('Medicine artifact not found.');
            
            // Inject metrics for detail view as well
            $uid = crc32($medicine['id'] . 'salt123');
            $medicine['distance'] = round((($uid % 50) + 5) / 10, 1);
            
            // Check for real reviews directly for single item
            $stmt = \App\Core\Database::getInstance()->getConnection()->prepare("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE medicine_id = ?");
            $stmt->execute([$id]);
            $stats = $stmt->fetch();

            if (!empty($stats['avg'])) {
                $medicine['rating'] = round((float)$stats['avg'], 1);
                $medicine['reviews_count'] = (int)$stats['total'];
            } else {
                $medicine['rating'] = round((($uid % 9) + 41) / 10, 1);
                $medicine['reviews_count'] = ($uid % 300) + 24;
            }

            return ResponseHandler::success(['medicine' => $medicine]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /medicines/{id}/substitutes
     */
    public function getSubstitutes($id) {
        try {
            $substitutes = (new \App\Models\MedicineSubstitute())->getByMedicine($id);
            return ResponseHandler::success(['substitutes' => $substitutes]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /categories
     */
    public function categories() {
        try {
            $categories = $this->categoryModel->findAll();
            return ResponseHandler::success(['categories' => $categories]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
