<?php
namespace App\Models;

use App\Core\Model;

/**
 * WorkerJob Model
 */
class WorkerJob extends Model {
    protected $table = 'worker_jobs';

    /**
     * Fetch next available job from queue
     */
    public function getNext($queue = 'default') {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` 
                                    WHERE queue = ? AND available_at <= NOW() AND reserved_at IS NULL 
                                    ORDER BY created_at ASC LIMIT 1");
        $stmt->execute([$queue]);
        return $stmt->fetch();
    }
}
