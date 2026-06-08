<?php
namespace App\Models;

use App\Core\Model;

/**
 * FailedJob Model
 */
class FailedJob extends Model {
    protected $table = 'failed_jobs';

    /**
     * Log a failed job
     */
    public function log($connection, $queue, $payload, $exception) {
        return $this->create([
            'connection' => $connection,
            'queue' => $queue,
            'payload' => $payload,
            'exception' => $exception
        ]);
    }
}
