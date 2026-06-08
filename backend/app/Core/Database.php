<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Core Engine
 * Singleton class to manage PDO connections for the application context.
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            $logPath = dirname(__DIR__, 2) . '/storage/logs/error.log';
            $timestamp = date('Y-m-d H:i:s');
            $entry = "[$timestamp] CRITICAL DB ERROR: " . $e->getMessage() . PHP_EOL;
            @file_put_contents($logPath, $entry, FILE_APPEND);
            
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database connection failed. Please check MySQL service.']);
            exit;
        }
    }

    /**
     * @return self
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->pdo;
    }
}
