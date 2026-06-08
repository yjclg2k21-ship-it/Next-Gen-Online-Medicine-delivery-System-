<?php
namespace App\Models;

use App\Core\Model;

/**
 * SupportTicket Model
 */
class SupportTicket extends Model {
    protected $table = 'support_tickets';
    protected $useSoftDelete = false;

    /**
     * Get tickets by user ID
     */
    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT t.*, u.name as user_name FROM `{$this->table}` t JOIN users u ON t.user_id = u.id WHERE t.user_id = ? ORDER BY t.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all tickets with user names (Admin)
     */
    public function getAllWithUsers() {
        $stmt = $this->db->query("SELECT t.*, u.name as user_name FROM `{$this->table}` t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Add message to ticket
     */
    public function addMessage($ticketId, $userId, $message) {
        $stmt = $this->db->prepare("INSERT INTO support_ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$ticketId, $userId, $message]);
    }

    /**
     * Get ticket messages
     */
    public function getMessages($ticketId) {
        $stmt = $this->db->prepare("SELECT m.*, u.name as user_name FROM support_ticket_messages m 
                                    JOIN users u ON m.user_id = u.id 
                                    WHERE m.ticket_id = ? ORDER BY m.created_at ASC");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
}
