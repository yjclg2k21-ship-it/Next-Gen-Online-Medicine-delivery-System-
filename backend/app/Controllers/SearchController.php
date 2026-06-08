<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\ResponseHandler;
use Exception;

/**
 * Search Controller — Medicine search with salt-based matching, filters & suggestions
 * Logic Parity: Fuzzy search on name/salt, category filtering, and smart suggestions.
 */
class SearchController extends BaseController {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function search() {
        $q       = $_GET['q']       ?? '';
        $salt    = $_GET['salt']    ?? '';
        $category= $_GET['category']?? '';
        $sort    = $_GET['sort']    ?? 'relevance';

        try {
            $sql = "SELECT m.*, b.name as brand_name, c.name as category_name 
                    FROM medicines m 
                    LEFT JOIN brands b ON m.brand_id = b.id 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    WHERE m.approval_status = 'approved' AND m.deleted_at IS NULL AND m.vendor_id = 2";
            $params = [];

            if ($q) {
                $sql .= " AND (m.name LIKE ? OR m.salt LIKE ? OR b.name LIKE ?)";
                $searchTerm = "%$q%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if ($salt) {
                $sql .= " AND m.salt = ?";
                $params[] = $salt;
            }

            if ($category) {
                $sql .= " AND c.name = ?";
                $params[] = $category;
            }

            switch ($sort) {
                case 'price_low': $sql .= " ORDER BY m.price ASC"; break;
                case 'price_high': $sql .= " ORDER BY m.price DESC"; break;
                default: $sql .= " ORDER BY m.created_at DESC";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();

            // Smart suggestions using partial match on names
            $suggStmt = $this->db->prepare("SELECT name FROM medicines WHERE name LIKE ? AND vendor_id = 2 LIMIT 5");
            $suggStmt->execute(["%$q%"]);
            $suggestions = $suggStmt->fetchAll(\PDO::FETCH_COLUMN);

            return ResponseHandler::success([
                'query'       => $q,
                'total'       => count($results),
                'results'     => $results,
                'suggestions' => $suggestions,
            ]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function suggestions() {
        $q = $_GET['q'] ?? '';
        try {
            $stmt = $this->db->prepare("SELECT id, name, salt FROM medicines WHERE (name LIKE ? OR salt LIKE ?) AND approval_status = 'approved' AND deleted_at IS NULL AND vendor_id = 2 LIMIT 10");
            $stmt->execute(["%$q%", "%$q%"]);
            return ResponseHandler::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
