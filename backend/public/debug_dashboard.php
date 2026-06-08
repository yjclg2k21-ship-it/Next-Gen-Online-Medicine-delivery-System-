<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

// Check SQL mode
$mode = $db->query("SELECT @@sql_mode")->fetchColumn();
echo "SQL Mode: " . $mode . "\n\n";

// Check PDO error mode
$errMode = $db->getAttribute(\PDO::ATTR_ERRMODE);
echo "PDO Error Mode: " . $errMode . " (0=SILENT, 1=WARNING, 2=EXCEPTION)\n\n";

// Test the exact query from dashboard with NOT IN containing non-enum values
echo "Testing dashboard queries:\n";
try {
    $s = $db->prepare("SELECT COUNT(*) FROM `orders` WHERE deleted_at IS NULL AND (user_id = ? AND status NOT IN ('delivered', 'cancelled', 'return_requested', 'returned'))");
    $s->execute([1]);
    echo "  count query: OK -> " . $s->fetchColumn() . "\n";
} catch (\Exception $e) {
    echo "  count query FAILED: " . $e->getMessage() . "\n";
}

try {
    $s = $db->prepare("SELECT *, IF(is_emergency = 1, 'Emergency', 'Standard') as delivery_type FROM `orders` WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC");
    $s->execute([1]);
    $rows = $s->fetchAll();
    echo "  getByUserId query: OK -> " . count($rows) . " rows\n";
} catch (\Exception $e) {
    echo "  getByUserId query FAILED: " . $e->getMessage() . "\n";
}

// Check if RevokedToken table exists (AuthGuard checks this)
try {
    $s = $db->query("SELECT COUNT(*) FROM `revoked_tokens`");
    echo "  revoked_tokens table: OK -> " . $s->fetchColumn() . " rows\n";
} catch (\Exception $e) {
    echo "  revoked_tokens table FAILED: " . $e->getMessage() . "\n";
}

// Simulate full dashboard call
echo "\nFull dashboard simulation:\n";
try {
    $orderModel = new \App\Models\Order();
    $notifModel = new \App\Models\Notification();
    
    $stats = [
        'total_orders' => $orderModel->count("user_id = ?", [1]),
        'active_orders' => $orderModel->count("user_id = ? AND status NOT IN ('delivered', 'cancelled', 'return_requested', 'returned')", [1]),
        'total_spent' => $orderModel->sum('grand_total', "user_id = ? AND status = 'delivered'", [1])
    ];
    echo "  Stats: " . json_encode($stats) . "\n";
    
    $recent = $orderModel->getByUserId(1);
    echo "  Recent orders: " . count($recent) . "\n";
    
    $unread = $notifModel->count("user_id = ? AND is_read = 0", [1]);
    echo "  Unread notifs: " . $unread . "\n";
    
    echo "\nALL OK - Dashboard should work!\n";
} catch (\Exception $e) {
    echo "  FAILED: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
}
