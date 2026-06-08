<?php
namespace App\Controllers;

use App\Core\Database;
use App\Middleware\AuthGuard;
use Exception;

/**
 * Review Controller — Submit, fetch and moderate reviews
 */
class ReviewController extends BaseController {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    private function getUserId() {
        AuthGuard::handle();
        return AuthGuard::getUser()['id'];
    }

    public function index($medicineId) {
        try {
            $stmt = $this->db->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.medicine_id = ? ORDER BY r.created_at DESC");
            $stmt->execute([$medicineId]);
            $reviews = $stmt->fetchAll();

            $avgStmt = $this->db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews WHERE medicine_id = ?");
            $avgStmt->execute([$medicineId]);
            $stats = $avgStmt->fetch();

            return $this->json([
                'reviews' => $reviews, 
                'average_rating' => round((float)$stats['avg_rating'], 1), 
                'total' => (int)$stats['total']
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create() {
        try {
            $userId = $this->getUserId();
            $data = $this->getPostData();
            if (empty($data['medicine_id']) || empty($data['rating'])) {
                return $this->json(['success' => false, 'message' => 'medicine_id and rating required'], 400);
            }

            // Sync: 'review_text' from frontend maps to 'comment' in DB
            $comment = $data['review_text'] ?? ($data['comment'] ?? null);

            $stmt = $this->db->prepare("INSERT INTO reviews (user_id, medicine_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $userId, 
                $data['medicine_id'],
                $data['rating'],
                $comment
            ]);

            return $this->json(['success' => true, 'message' => 'Review submitted successfully', 'review_id' => $this->db->lastInsertId()], 201);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$id]);
            return $this->json(['success' => true, 'message' => "Review #$id deleted"]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
