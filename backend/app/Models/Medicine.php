<?php
namespace App\Models;

use App\Core\Model;

/**
 * Medicine Model
 */
class Medicine extends Model {
    protected $table = 'medicines';
    protected $useSoftDelete = true;

    /**
     * Override findAll to include category names and real ratings
     */
    public function findAll() {
        $sql = "SELECT m.*, c.name as category_name, b.name as brand_name, b.type as brand_type, u.name as store_name, 
                       r.avg_rating, r.total_reviews
                FROM `medicines` m 
                LEFT JOIN `categories` c ON m.category_id = c.id 
                LEFT JOIN `brands` b ON m.brand_id = b.id
                LEFT JOIN `users` u ON m.vendor_id = u.id
                LEFT JOIN (
                    SELECT medicine_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                    FROM reviews GROUP BY medicine_id
                ) r ON m.id = r.medicine_id
                WHERE m.deleted_at IS NULL AND m.vendor_id = 2";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Override findById to include metadata
     */
    public function findById($id) {
        $sql = "SELECT m.*, c.name as category_name, b.name as brand_name, b.type as brand_type, u.name as store_name
                FROM `medicines` m 
                LEFT JOIN `categories` c ON m.category_id = c.id 
                LEFT JOIN `brands` b ON m.brand_id = b.id
                LEFT JOIN `users` u ON m.vendor_id = u.id
                WHERE m.id = ? AND m.deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Search medicines by name or salt
     */
    public function search($query) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` 
                                    WHERE (name LIKE ? OR salt LIKE ?) 
                                    AND deleted_at IS NULL AND approval_status = 'approved' AND vendor_id = 2");
        $stmt->execute(['%'.$query.'%', '%'.$query.'%']);
        return $stmt->fetchAll();
    }

    /**
     * Get substitutes by medicine ID
     */
    public function getSubstitutes($id) {
        $sql = "SELECT m.* FROM `medicines` m 
                JOIN `medicine_substitutes` s ON m.id = s.substitute_id 
                WHERE s.medicine_id = ? AND m.deleted_at IS NULL";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * Get low stock medicines for vendor
     */
    public function getLowStock($vendorId, $threshold = 15, $limit = 8) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE vendor_id = ? AND stock <= ? AND deleted_at IS NULL LIMIT " . (int)$limit);
        $stmt->execute([$vendorId, $threshold]);
        return $stmt->fetchAll();
    }

    /**
     * Get expiring medicines for vendor (within next 60 days)
     */
    public function getExpiringSoon($vendorId, $days = 60, $limit = 8) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` 
                                    WHERE vendor_id = ? 
                                    AND expiry_date IS NOT NULL 
                                    AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                                    AND expiry_date >= CURDATE()
                                    AND deleted_at IS NULL LIMIT " . (int)$limit);
        $stmt->execute([$vendorId, $days]);
        return $stmt->fetchAll();
    }

    /**
     * Get vendor catalog
     */
    public function getVendorCatalog($vendorId) {
        $sql = "SELECT m.id, m.name, m.salt as generic, m.stock, m.price, m.expiry_date as expiry, 
                       m.approval_status, m.status as visible, c.name as category 
                FROM `{$this->table}` m 
                LEFT JOIN `categories` c ON m.category_id = c.id 
                WHERE m.vendor_id = ? AND m.deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get top selling medicines for a vendor
     */
    public function getTopSelling($vendorId, $limit = 5) {
        $sql = "SELECT m.name, SUM(oi.quantity) as sales, SUM(oi.quantity * oi.price) as revenue
                FROM order_items oi 
                JOIN medicines m ON oi.medicine_id = m.id 
                WHERE m.vendor_id = ? AND m.deleted_at IS NULL 
                GROUP BY m.id ORDER BY sales DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all medicines for a vendor (for vendor medicine list page)
     */
    public function getByVendor($vendorId) {
        $sql = "SELECT m.*, c.name as category_name
                FROM `{$this->table}` m
                LEFT JOIN `categories` c ON m.category_id = c.id
                WHERE m.vendor_id = ? AND m.deleted_at IS NULL
                ORDER BY m.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }
}
