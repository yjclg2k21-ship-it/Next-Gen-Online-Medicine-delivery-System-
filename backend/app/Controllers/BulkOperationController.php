<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\Medicine;
use App\Models\AuditLog;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use Exception;

/**
 * Bulk Operation Controller — Aligned with Unified Model Architecture
 */
class BulkOperationController extends BaseController {
    
    private $medicineModel;
    private $auditModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
        $this->auditModel = new AuditLog();
    }

    /**
     * GET /bulk/jobs
     */
    public function index() {
        AuthGuard::handle();
        RoleCheck::handle('admin');
        
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT id, action as type, created_at as time, metadata FROM audit_logs WHERE action = 'BULK_IMPORT' ORDER BY created_at DESC");
            $logs = $stmt->fetchAll();

            $jobs = [];
            foreach ($logs as $log) {
                $meta = json_decode($log['metadata'] ?? '{}', true);
                $jobs[] = [
                    'id' => 'JOB-' . $log['id'],
                    'type' => 'Medicine Import',
                    'status' => 'Completed',
                    'items' => $meta['count'] ?? 0,
                    'time' => $log['time']
                ];
            }
            return ResponseHandler::success(['jobs' => $jobs]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * POST /bulk/upload
     */
    public function uploadCsv() {
        AuthGuard::handle();
        RoleCheck::handle(['admin', 'vendor']);
        $user = AuthGuard::getUser();

        if (empty($_FILES['csv']['tmp_name'])) {
            return ResponseHandler::badRequest('No clinical dataset (CSV) detected.');
        }

        $file = $_FILES['csv']['tmp_name'];
        $handle = fopen($file, "r");
        
        $headers = fgetcsv($handle); // Assuming: name, price, stock, description
        $count = 0;
        
        try {
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (count($row) < 3) continue;
                
                $this->medicineModel->create([
                    'vendor_id' => $user['id'],
                    'name' => $row[0],
                    'price' => (float)$row[1],
                    'stock' => (int)$row[2],
                    'description' => $row[3] ?? '',
                    'approval_status' => 'approved', // Auto-approve bulk for demo parity
                    'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row[0]))) . '-' . rand(100,999)
                ]);
                $count++;
            }
            fclose($handle);

            $this->auditModel->log($user['id'], 'BULK_IMPORT', 'Medicine', 0, ['count' => $count]);
            return ResponseHandler::success(['processed' => $count], 'Dataset ingested and propagation complete.');
            
        } catch (Exception $e) {
            return ResponseHandler::error('Dataset corruption: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /bulk/status/{id}
     */
    public function checkStatus($id) {
        AuthGuard::handle();
        try {
            $idOnly = str_replace('JOB-', '', $id);
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM audit_logs WHERE id = ? AND action = 'BULK_IMPORT'");
            $stmt->execute([$idOnly]);
            $log = $stmt->fetch();

            if (!$log) return ResponseHandler::notFound('Job artifact not found.');

            $meta = json_decode($log['metadata'] ?? '{}', true);
            return ResponseHandler::success([
                'id' => $id,
                'status' => 'Completed',
                'progress' => 100,
                'processed' => $meta['count'] ?? 0,
                'started_at' => $log['created_at']
            ]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
