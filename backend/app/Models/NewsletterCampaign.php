<?php
namespace App\Models;

use App\Core\Model;

/**
 * NewsletterCampaign Model
 */
class NewsletterCampaign extends Model {
    protected $table = 'newsletter_campaigns';
    protected $useSoftDelete = false;

    /**
     * Get sent campaigns
     */
    public function getSent() {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` WHERE sent_at IS NOT NULL ORDER BY sent_at DESC");
        return $stmt->fetchAll();
    }
}
