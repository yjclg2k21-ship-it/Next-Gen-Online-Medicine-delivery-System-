<?php
// Migration: Add sunday columns to vendor_profiles
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    $columns = [
        'sunday_open'          => "TINYINT(1) DEFAULT 0",
        'sunday_opening_time'  => "TIME DEFAULT NULL",
        'sunday_closing_time'  => "TIME DEFAULT NULL",
    ];

    foreach ($columns as $col => $def) {
        $stmt = $db->query("SHOW COLUMNS FROM vendor_profiles LIKE '$col'");
        if ($stmt->fetch()) {
            echo "<p style='color:orange;'>Column <b>$col</b> already exists. Skipping.</p>";
        } else {
            $db->exec("ALTER TABLE vendor_profiles ADD COLUMN `$col` $def");
            echo "<p style='color:green;'>✅ Column <b>$col</b> added successfully.</p>";
        }
    }
    echo "<p style='color:green;font-weight:bold;'>Migration complete.</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
