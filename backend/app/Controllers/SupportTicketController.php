<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use Exception;

/**
 * Support Ticket Controller — Aligned with Unified Model Architecture
 */
class SupportTicketController extends BaseController {

    private $ticketModel;
    private $messageModel;

    public function __construct() {
        $this->ticketModel = new SupportTicket();
        $this->messageModel = new SupportTicketMessage();
    }

    /**
     * GET /admin/tickets
     */
    public function index() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        try {
            // If admin, show all, else show user specific
            if ($user['role'] === 'admin') {
                $tickets = $this->ticketModel->getAllWithUsers();
            } else {
                $tickets = $this->ticketModel->getByUser($user['id']);
            }
            return ResponseHandler::success(['tickets' => $tickets]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /admin/tickets (Create)
     */
    public function create() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        try {
            $id = $this->ticketModel->create([
                'user_id' => $user['id'],
                'subject' => $data['subject'] ?? 'System Inquiry',
                'status'  => 'open',
                'priority'=> $data['priority'] ?? 'medium'
            ]);
            return ResponseHandler::success(['id' => $id], 'Support protocol initiated.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/tickets/{id}
     */
    public function show($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        try {
            $ticket = $this->ticketModel->findById($id);
            if (!$ticket) return ResponseHandler::notFound();

            // Authorization check
            if ($user['role'] !== 'admin' && $ticket['user_id'] != $user['id']) {
                return ResponseHandler::forbidden();
            }

            $ticket['messages'] = $this->messageModel->getByTicket($id);
            return ResponseHandler::success(['ticket' => $ticket]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /admin/tickets/{id}/reply
     */
    public function reply($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        try {
            $ticket = $this->ticketModel->findById($id);
            if (!$ticket) return ResponseHandler::notFound();

            $this->messageModel->create([
                'ticket_id' => $id,
                'user_id'   => $user['id'],
                'message'   => $data['message'] ?? ''
            ]);

            // Update ticket status
            $newStatus = ($user['role'] === 'admin') ? 'in_progress' : 'open';
            $this->ticketModel->update($id, ['status' => $newStatus]);

            return ResponseHandler::success([], 'Message synchronized.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
