<?php
namespace App\Services;

/**
 * Notification Service
 * Universal service for Dispatching Push, SMS, and Email alerts.
 */
class NotificationService {

    public function sendPushNotification($userId, $title, $body, $type = 'info') {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $title, $body, $type]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendEmail($email, $subject, $template, $data = []) {
        // Production: Integrate with PHPMailer or SendGrid node
        error_log("Production Trace: Email dispatch initiated for $email - $subject");
        return true;
    }

    public function sendSms($phone, $message) {
        // Production: Integrate with Twilio or Msg91 gateway
        error_log("Production Trace: SMS dispatch initiated for $phone");
        return true;
    }

    public function alertAdmin($subject, $level = 'info') {
        // Production: Broadcast to Admin via WebSocket or Dedicated Audit Node
        $db = \App\Core\Database::getInstance()->getConnection();
        $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (1, ?, ?, ?)")
           ->execute([$subject, 'Admin System Alert', $level]);
        return true;
    }

    /**
     * Universal dispatcher (Parity Logic)
     */
    public function send($userId, $title, $body) {
        return $this->sendPushNotification($userId, $title, $body);
    }
}
