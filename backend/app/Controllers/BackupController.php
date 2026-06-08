<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use App\Core\Database;
use Exception;
use PDO;

class BackupController extends BaseController {

    private $db;
    private $backupPath;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->backupPath = dirname(__DIR__, 2) . '/storage/backups';
        if (!file_exists($this->backupPath)) {
            mkdir($this->backupPath, 0777, true);
        }
    }

    /**
     * GET /admin/backups
     */
    public function index() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $stmt = $this->db->query("SELECT * FROM backups ORDER BY created_at DESC");
        $backups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ResponseHandler::success([
            'backups' => $backups
        ], 'Backup registry retrieved.');
    }

    /**
     * POST /admin/backups
     */
    public function create() {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $data = $this->getPostData();
        $type = $data['type'] ?? 'manual';
        $timestamp = date('Y-m-d_His');
        $filename = "backup_{$type}_{$timestamp}.sql";
        $fullPath = $this->backupPath . '/' . $filename;
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

        // XAMPP Specific Path for mysqldump
        $mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';
        $dbName = 'medicine_delivery';
        $user = 'root';
        $pass = '';

        $passArg = $pass ? "--password=\"$pass\"" : "";
        $ignoreTable = "--ignore-table={$dbName}.backups";
        $cmd = "\"$mysqldump\" --user=$user $passArg $ignoreTable $dbName > \"$fullPath\"";
        
        try {
            exec($cmd, $output, $returnVar);

            if (!file_exists($fullPath) || filesize($fullPath) < 100) {
                $err = implode("\n", $output);
                throw new Exception("Backup file not created or too small. Output: $err");
            }

            $sizeBytes = filesize($fullPath);
            $sizeFormatted = round($sizeBytes / (1024 * 1024), 2) . ' MB';

            $stmt = $this->db->prepare("INSERT INTO backups (filename, size, type, status) VALUES (?, ?, ?, 'success')");
            $stmt->execute([$filename, $sizeFormatted, $type]);

            return ResponseHandler::success([
                'id' => $this->db->lastInsertId(),
                'filename' => $filename
            ], 'Database snapshot captured successfully.');

        } catch (Exception $e) {
            if (file_exists($fullPath)) unlink($fullPath);
            return ResponseHandler::error('Backup capture failure: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /admin/backups/{id}/restore
     */
    public function restore($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $stmt = $this->db->prepare("SELECT * FROM backups WHERE id = ?");
        $stmt->execute([$id]);
        $backup = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$backup) return ResponseHandler::error('Backup record not found.');

        $fullPath = $this->backupPath . '/' . $backup['filename'];
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);
        
        if (!file_exists($fullPath)) return ResponseHandler::error('Backup file missing from storage.');

        // XAMPP Specific Path for mysql
        $mysql = 'C:\xampp\mysql\bin\mysql.exe';
        $dbName = 'medicine_delivery';
        $user = 'root';
        $pass = '';

        $passArg = $pass ? "--password=\"$pass\"" : "";
        $cmd = "\"$mysql\" --user=$user $passArg $dbName < \"$fullPath\"";

        try {
            exec($cmd, $output, $returnVar);
            if ($returnVar !== 0) throw new Exception("mysql restore failed with exit code $returnVar");

            return ResponseHandler::success([], 'Pharmaceutical ledger re-anchored successfully.');
        } catch (Exception $e) {
            return ResponseHandler::error('Restoration critical failure: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /admin/backups/{id}/download
     */
    public function download($id) {
        AuthGuard::handle();
        RoleCheck::handle('admin');

        $stmt = $this->db->prepare("SELECT * FROM backups WHERE id = ?");
        $stmt->execute([$id]);
        $backup = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$backup) die('Backup not found.');

        $fullPath = $this->backupPath . '/' . $backup['filename'];
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

        if (!file_exists($fullPath)) die('File missing.');

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }
}
