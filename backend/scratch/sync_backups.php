<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();

$backupPath = dirname(__DIR__) . '/storage/backups';
$files = glob($backupPath . '/*.sql');

foreach ($files as $file) {
    $filename = basename($file);
    $stmt = $db->prepare("SELECT id FROM backups WHERE filename = ?");
    $stmt->execute([$filename]);
    if (!$stmt->fetch()) {
        $sizeBytes = filesize($file);
        $sizeFormatted = round($sizeBytes / (1024 * 1024), 2) . ' MB';
        $type = 'manual';
        
        $stmt = $db->prepare("INSERT INTO backups (filename, size, type, status) VALUES (?, ?, ?, 'success')");
        $stmt->execute([$filename, $sizeFormatted, $type]);
        echo "Restored record for $filename\n";
    }
}
echo "Sync complete.\n";
