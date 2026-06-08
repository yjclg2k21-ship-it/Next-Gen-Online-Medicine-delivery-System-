<?php
namespace App\Controllers;

use App\Core\Database;
use Exception;

/**
 * Brand Controller
 * Handles medicine brand information, logos, and association mapping.
 */
class BrandController extends BaseController {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, (SELECT COUNT(*) FROM medicines WHERE brand_id = b.id) as products 
                FROM brands b 
                WHERE b.status = 1 
                ORDER BY b.name ASC
            ");
            $stmt->execute();
            $brands = $stmt->fetchAll();
            return $this->json(['brands' => $brands]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create() {
        try {
            $data = $this->getPostData();
            if (empty($data['name'])) {
                return $this->json(['success' => false, 'message' => 'Brand name required'], 400);
            }

            $stmt = $this->db->prepare("INSERT INTO brands (name, slug, hq, type, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $slug = strtolower(str_replace(' ', '-', $data['name']));
            $stmt->execute([
                $data['name'], 
                $slug,
                $data['hq'] ?? 'N/A',
                $data['type'] ?? 'Domestic',
                1 // Status active
            ]);

            return $this->json(['success' => true, 'message' => 'Brand created successfully', 'brand_id' => $this->db->lastInsertId()], 201);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $data = $this->getPostData();
            if (empty($data['name'])) {
                return $this->json(['success' => false, 'message' => 'Brand name required'], 400);
            }

            $stmt = $this->db->prepare("UPDATE brands SET name = ?, slug = ?, hq = ?, type = ? WHERE id = ?");
            $slug = strtolower(str_replace(' ', '-', $data['name']));
            $stmt->execute([
                $data['name'],
                $slug,
                $data['hq'] ?? 'N/A',
                $data['type'] ?? 'Domestic',
                $id
            ]);

            return $this->json(['success' => true, 'message' => 'Brand updated successfully']);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM brands WHERE id = ?");
            $stmt->execute([$id]);
            return $this->json(['success' => true, 'message' => "Brand $id deleted."]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
