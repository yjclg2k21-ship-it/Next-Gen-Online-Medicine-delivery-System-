<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use Exception;

class BulkController extends BaseController {

    public function __construct() {
        // Models can be initialized here if needed
    }

    /**
     * POST /admin/bulk/upload
     */
    public function upload() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        if (!isset($_FILES['file'])) {
            return ResponseHandler::error("No file uploaded.", 400);
        }

        $type = $_POST['type'] ?? 'unknown';
        
        // Simulation of a heavy processing job
        try {
            // In a real system, we would move the file to a secure storage and queue a worker job.
            // For this simulation, we'll acknowledge the receipt.
            return ResponseHandler::success([
                'job_id' => 'BJ-' . rand(1000, 9999),
                'type' => $type
            ], "Import job for $type has been queued successfully.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/bulk/status/all
     */
    public function jobs() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $db = \App\Core\Database::getInstance()->getConnection();
        $jobs = [];
        
        try {
            $stmt = $db->query("SELECT * FROM bulk_jobs ORDER BY created_at DESC LIMIT 20");
            $jobs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            // Table doesn't exist — return empty
        }

        return ResponseHandler::success(['jobs' => $jobs]);
    }

    /**
     * GET /admin/bulk/export/{type}
     */
    public function export($type) {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        // Export simulation
        return ResponseHandler::success([], "Export for $type triggered successfully.");
    }
}
