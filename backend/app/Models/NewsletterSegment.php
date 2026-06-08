<?php
namespace App\Models;

use App\Core\Model;

/**
 * NewsletterSegment Model
 */
class NewsletterSegment extends Model {
    protected $table = 'newsletter_segments';

    public function getAll() {
        return $this->findAll();
    }
}
