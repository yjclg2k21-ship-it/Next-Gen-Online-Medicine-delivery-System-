<?php
namespace App\Controllers;

use App\Core\Database;
use Exception;

/**
 * Marketing Controller
 * Manages banners, newsletters, and promotional campaigns.
 */
class MarketingController extends BaseController {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function banners() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM banners WHERE status = 'active' ORDER BY created_at DESC");
            $stmt->execute();
            $banners = $stmt->fetchAll();
            return $this->json(['banners' => $banners]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function newsletters() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM newsletter_campaigns ORDER BY created_at DESC");
            $stmt->execute();
            $campaigns = $stmt->fetchAll();
            return $this->json(['campaigns' => $campaigns]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
