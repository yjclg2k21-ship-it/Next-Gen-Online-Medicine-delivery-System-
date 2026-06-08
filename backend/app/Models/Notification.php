<?php
namespace App\Models;

use App\Core\Model;

/**
 * Notification Model
 */
class Notification extends Model {
    protected $table = 'notifications';

    public function getByUser($userId, $limit = 50) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` 
                                    WHERE user_id = ? 
                                    ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM `{$this->table}` 
                                    WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    }

    public function markAsRead($id, $userId) {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
