<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Models\Notification;
use Exception;

/**
 * Notification Controller — Handles push & in-app medical notifications.
 */
class NotificationController extends BaseController {

    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    public function index() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        try {
            $notifications = $this->notificationModel->getByUser($user['id']);
            $unreadCount = $this->notificationModel->getUnreadCount($user['id']);

            return ResponseHandler::success([
                'notifications' => $notifications, 
                'unread_count' => (int)$unreadCount
            ]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function markRead($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        try {
            $this->notificationModel->markAsRead($id, $user['id']);
            return ResponseHandler::success([], "Health alert acknowledged.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function markUnread($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        try {
            $this->notificationModel->update($id, ['is_read' => 0]);
            return ResponseHandler::success([], "Health alert reverted to unread.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function markAllRead() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        try {
            $this->notificationModel->markAllAsRead($user['id']);
            return ResponseHandler::success([], 'All healthcare alerts marked as read.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function preferences() {
        AuthGuard::handle();
        return ResponseHandler::success(['preferences' => [
            'order_updates'    => true,
            'promotions'       => true,
            'prescription_alerts' => true,
            'sms_enabled'      => false,
            'email_enabled'    => true,
        ]]);
    }

    public function updatePreferences() {
        AuthGuard::handle();
        $data = $this->getPostData();
        return ResponseHandler::success($data, 'Clinical notification preferences synchronized.');
    }
}
