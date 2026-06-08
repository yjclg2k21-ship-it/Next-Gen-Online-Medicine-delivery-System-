<?php
namespace App\Models;

use App\Core\Model;

/**
 * NewsletterSubscriber Model
 */
class NewsletterSubscriber extends Model {
    protected $table = 'newsletter_subscribers';
    protected $useSoftDelete = false;

    /**
     * Find by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
