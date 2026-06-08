<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use Exception;

/**
 * Pharmacy Inventory Controller
 * Detailed inventory audit and global stock level monitoring for admins and central management.
 */
class PharmacyInventoryController extends BaseController {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function globalStock() {
        AuthGuard::handle();
        RoleCheck::handle(['admin', 'vendor']);
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_skus,
                    SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                    SUM(CASE WHEN expiry_date <= DATE_ADD(NOW(), INTERVAL 90 DAY) THEN 1 ELSE 0 END) as expiring_soon,
                    SUM(stock * price) as total_valuation
                FROM medicines
            ");
            $stmt->execute();
            $stats = $stmt->fetch();

            return ResponseHandler::success([
                'total_skus' => (int)$stats['total_skus'],
                'out_of_stock' => (int)$stats['out_of_stock'],
                'expiring_soon' => (int)$stats['expiring_soon'],
                'total_valuation' => (float)$stats['total_valuation']
            ]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function expiryReport() {
        AuthGuard::handle();
        RoleCheck::handle(['admin', 'vendor']);
        try {
            // Expiring in next 30 days
            $stmt = $this->db->prepare("
                SELECT m.name, m.stock as qty, m.expiry_date, u.name as vendor 
                FROM medicines m 
                JOIN users u ON m.vendor_id = u.id 
                WHERE m.expiry_date <= DATE_ADD(NOW(), INTERVAL 30 DAY) 
                ORDER BY m.expiry_date ASC
            ");
            $stmt->execute();
            $expiring = $stmt->fetchAll();

            return ResponseHandler::success(['expiring_soon' => $expiring]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function index() {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        try {
            $user = AuthGuard::getUser();
            $vendorId = $user['id'];
            
            $stmt = $this->db->prepare("SELECT m.*, c.name as category_name FROM medicines m LEFT JOIN categories c ON m.category_id = c.id WHERE m.vendor_id = ? AND m.deleted_at IS NULL ORDER BY m.created_at DESC");
            $stmt->execute([$vendorId]);
            $inventory = $stmt->fetchAll();
            
            return ResponseHandler::success(['inventory' => $inventory]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function update($id) {
        AuthGuard::handle();
        RoleCheck::handle('vendor');
        try {
            $data = $this->getPostData();
            $stock = (int)($data['stock'] ?? 0);
            $price = (float)($data['price'] ?? 0);
            
            $stmt = $this->db->prepare("UPDATE medicines SET stock = ?, price = ? WHERE id = ?");
            $stmt->execute([$stock, $price, $id]);
            
            return ResponseHandler::success([], 'Inventory node synchronized.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
