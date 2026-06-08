<?php
namespace App\Models;

use App\Core\Model;

/**
 * SupportTicketMessage Model
 */
class SupportTicketMessage extends Model {
    protected $table = 'support_ticket_messages';
    protected $useSoftDelete = false;

    /**
     * Get messages for a ticket
     */
    public function getByTicket($ticketId) {
        $stmt = $this->db->prepare(
            "SELECT stm.*, u.name as sender_name, u.role as sender_role 
             FROM `{$this->table}` stm 
             JOIN users u ON stm.user_id = u.id 
             WHERE stm.ticket_id = ? 
             ORDER BY stm.created_at ASC"
        );
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
}
