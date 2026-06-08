<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query('DESCRIBE `orders`');
$cols = $stmt->fetchAll(\PDO::FETCH_ASSOC);
echo "COLUMNS:\n";
foreach ($cols as $col) {
    echo "  " . $col['Field'] . " (" . $col['Type'] . ")" . ($col['Null'] === 'YES' ? ' NULL' : ' NOT NULL') . "\n";
}
