<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use Exception;

/**
 * Newsletter Controller — Unified Schema Implementation
 */
class NewsletterController extends BaseController {

    private $subscriberModel;
    private $campaignModel;

    public function __construct() {
        $this->subscriberModel = new NewsletterSubscriber();
        $this->campaignModel = new NewsletterCampaign();
    }

    /**
     * GET /api/v1/admin/newsletter/campaigns
     */
    public function campaigns() {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            
            // Get campaigns
            $campaigns = [];
            try {
                $campaigns = $db->query("SELECT * FROM newsletter_campaigns ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
            
            // Get subscriber count
            $reach = 0;
            try {
                $reach = (int)$db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'")->fetchColumn();
            } catch (\Exception $e) {}

            $stats = [
                'active_reach' => $reach,
                'avg_open_rate' => '0%',
                'active_series' => count($campaigns)
            ];

            return ResponseHandler::success(['campaigns' => $campaigns, 'stats' => $stats], 'Communication history retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/admin/newsletter/segments
     */
    public function segments() {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT id, name FROM newsletter_segments");
            $segments = $stmt->fetchAll();

            if (empty($segments)) {
                $segments = [
                    ['id' => 1, 'name' => 'All Subscribers', 'size' => count($this->subscriberModel->findAll())]
                ];
            } else {
                foreach ($segments as &$s) {
                    $s['size'] = count($this->subscriberModel->findAll()); // Simplified for now
                }
            }
            return ResponseHandler::success(['segments' => $segments]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/admin/newsletter/campaign
     */
    public function createCampaign() {
        $data = $this->getPostData();
        try {
            $id = $this->campaignModel->create([
                'subject' => $data['subject'] ?? 'No Subject',
                'body' => $data['body'] ?? '',
                'sent_at' => null // To be sent by worker
            ]);
            return ResponseHandler::success(['id' => $id], 'Campaign submitted for propagation.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/newsletter/subscribe
     */
    public function subscribe() {
        $data = $this->getPostData();
        $email = $data['email'] ?? null;
        if (!$email) return ResponseHandler::error('Email required', 400);

        try {
            if ($this->subscriberModel->findByEmail($email)) {
                return ResponseHandler::success([], 'Already subscribed.');
            }
            $this->subscriberModel->create(['email' => $email, 'status' => 'active']);
            return ResponseHandler::success([], 'Email associated with clinical communications.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
