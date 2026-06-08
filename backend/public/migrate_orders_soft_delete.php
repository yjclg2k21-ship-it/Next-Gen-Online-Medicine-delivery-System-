<?php
/**
 * Migration: Add deleted_at column to orders table
 * Fixes: Dashboard data synchronization failed (SQLSTATE[42S22]: Column not found: 1054 Unknown column 'deleted_at')
 */
require_once __DIR__ . '/../app/bootstrap.php';

$db = \App\Core\Database::getInstance()->getConnection();

try {
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM `orders` LIKE 'deleted_at'");
    $exists = $stmt->fetchAll();
    
    if (count($exists) > 0) {
        echo json_encode(['success' => true, 'message' => 'Column already exists. No action needed.']);
        exit;
    }
    
    // Add deleted_at column
    $db->exec("ALTER TABLE `orders` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Successfully added deleted_at column to orders table.',
        'fix' => 'Dashboard data synchronization should now work correctly.'
    ]);
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
