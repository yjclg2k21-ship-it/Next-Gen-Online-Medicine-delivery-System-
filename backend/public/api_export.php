<?php
/**
 * Standalone Secure Export Handler
 * Bypasses main router to avoid header/buffer interference
 */
require_once __DIR__ . '/../app/bootstrap.php';

use App\Middleware\AuthGuard;
use App\Models\AuditLog;

try {
    // 1. Authenticate via Query Param
    if (!isset($_GET['token'])) {
        die("Unauthorized: Token missing");
    }
    
    
    AuthGuard::handle(); // This will use the GET token fallback I added earlier
    
    $user = AuthGuard::getUser();
    if (!$user || $user['role'] !== 'admin') {
        die("Unauthorized: Admin access required");
    }

    // 2. Fetch Logs
    $auditModel = new AuditLog();
    $logs = $auditModel->getLatest(500);
    
    // 3. Force Clean Export
    $filename = "medimitra_audit_trail_" . date('Y-m-d') . ".csv";
    
    while (ob_get_level()) ob_end_clean();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
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
    die("Export Error: " . $e->getMessage());
}
