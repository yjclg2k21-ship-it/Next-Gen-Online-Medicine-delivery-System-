<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Core\Database;
use Exception;

class RefillController extends BaseController {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        try {
            $stmt = $this->db->prepare("SELECT rr.*, m.name as medicine_name 
                                        FROM refill_reminders rr 
                                        JOIN medicines m ON rr.medicine_id = m.id 
                                        WHERE rr.user_id = ? 
                                        ORDER BY next_reminder_date ASC");
            $stmt->execute([$user['id']]);
            return ResponseHandler::success(['reminders' => $stmt->fetchAll()]);
        } catch (Exception $e) { return ResponseHandler::error($e->getMessage()); }
    }

    public function store() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        if (empty($data['medicine_id']) || empty($data['duration_days'])) {
            return ResponseHandler::badRequest("Medicine ID and cycle duration required.");
        }

        try {
            $lastOrderDate = $data['last_order_date'] ?? date('Y-m-d');
            $nextReminder = date('Y-m-d', strtotime($lastOrderDate . " + " . ($data['duration_days'] - 2) . " days"));

            $stmt = $this->db->prepare("INSERT INTO refill_reminders (user_id, medicine_id, last_order_date, duration_days, next_reminder_date) 
                                        VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user['id'], $data['medicine_id'], $lastOrderDate, $data['duration_days'], $nextReminder]);

            return ResponseHandler::success([], "Refill protocol scheduled for clinical consistency.", 201);
        } catch (Exception $e) { return ResponseHandler::error($e->getMessage()); }
    }
}
