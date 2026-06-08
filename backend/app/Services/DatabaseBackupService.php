<?php
namespace App\Services;

use App\Core\Database;
use PDO;
use Exception;

/**
 * Service to handle actual Database Backups
 */
class DatabaseBackupService {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a full SQL dump of the database
     */
    public function createBackup($filePath) {
        $tables = [];
        $stmt = $this->db->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- MediMitra Database Snapshot\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $sql .= "-- Table structure for `$table`\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $row = $this->db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            $sql .= $row[1] . ";\n\n";

            $sql .= "-- Dumping data for table `$table`\n";
            $stmt = $this->db->query("SELECT * FROM `$table`");
            $rowCount = $stmt->rowCount();
            
            if ($rowCount > 0) {
                $sql .= "INSERT INTO `$table` VALUES \n";
                $rowsData = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $values = array_map(function($value) {
                        if ($value === null) return "NULL";
                        return "'" . addslashes($value) . "'";
                    }, array_values($row));
                    $rowsData[] = "(" . implode(", ", $values) . ")";
                }
                $sql .= implode(",\n", $rowsData) . ";\n\n";
            }
        }

        file_put_contents($filePath, $sql);
        return round(filesize($filePath) / 1024 / 1024, 2); // Return size in MB
    }
}
