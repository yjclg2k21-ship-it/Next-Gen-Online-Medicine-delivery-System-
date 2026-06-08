<?php
header('Content-Type: application/json');

$config = require __DIR__ . '/../config/database.php';

$results = [
    'mysql_running' => false,
    'database_exists' => false,
    'connection_error' => null,
    'tables_found' => []
];

try {
    $dsn_no_db = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn_no_db, $config['username'], $config['password']);
    $results['mysql_running'] = true;

    $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['database']}'");
    if ($stmt->fetch()) {
        $results['database_exists'] = true;
        
        $pdo->exec("USE `{$config['database']}`");
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $results['tables_found'][] = $row[0];
        }
    }
} catch (PDOException $e) {
    $results['connection_error'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT);
