<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\AuditLog;
use App\Middleware\AuthGuard;
use Exception;

/**
 * Audit Controller — 100% Forensic Audit Trails & System Logs
 */
class AuditController extends BaseController {

    private $auditModel;

    public function __construct() {
        $this->auditModel = new AuditLog();
    }

    /**
     * GET /admin/logs/activity
     */
    public function activityLogs() {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            $logs = $this->auditModel->getLatest(200);
            $stats = $this->auditModel->getStats();
            
            return ResponseHandler::success([
                'logs' => $logs,
                'stats' => [
                    'logs_today' => (int)$stats['logs_today'],
                    'alerts' => (int)$stats['recent_alerts'],
                    'retention' => '100% FORENSIC'
                ]
            ], 'Forensic audit trails retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/logs/system
     */
    public function systemLogs() {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        // System logs pull from the standardized storage directory
        try {
            $logPath = dirname(__DIR__, 2) . '/storage/logs/error.log';
            $logs = [];
            if (file_exists($logPath)) {
                $lines = array_slice(file($logPath), -100); // Last 100 lines
                foreach ($lines as $i => $line) {
                    $logs[] = [
                        'id' => $i + 1,
                        'level' => str_contains($line, 'ERROR') ? 'ERROR' : 'INFO',
                        'actor' => 'System-Node',
                        'msg' => trim($line),
                        'timestamp' => substr($line, 0, 19)
                    ];
                }
            }
            return ResponseHandler::success(['logs' => array_reverse($logs)], 'System health logs retrieved.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/audit/{id}
     */
    public function auditDetail($id) {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        $log = $this->auditModel->findWithDetails($id);
        if (!$log) return ResponseHandler::notFound('Audit record missing.');

        return ResponseHandler::success($log, 'Forensic detail retrieved.');
    }

    /**
     * GET /admin/audit/export
     */
    public function export() {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            $logs = $this->auditModel->findAll();
            $filename = "audit_log.csv";
            
            // Clear all buffers
            while (ob_get_level()) ob_end_clean();
            
            header('Content-Type: application/force-download');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Transfer-Encoding: binary');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['LOG_ID', 'USER_NAME', 'ACTION', 'MODULE', 'REF_ID', 'TIMESTAMP', 'DETAILS']);
            
            foreach ($logs as $log) {
                fputcsv($output, [
                    $log['id'],
                    $log['user_name'] ?? ('User #'.$log['user_id']),
                    $log['action'],
                    $log['target_model'] ?? 'SYSTEM',
                    $log['target_id'] ?? '-',
                    $log['created_at'],
                    str_replace(['"', '{', '}', ':'], '', $log['payload'] ?? 'No extra data')
                ]);
            }
            fclose($output);
            exit;
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
