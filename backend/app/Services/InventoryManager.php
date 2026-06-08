<?php
namespace App\Services;

use App\Core\Database;

/**
 * InventoryManager Service
 * Handles stock reservation, confirmation, and restoration.
 * Critical for preventing overselling.
 */
class InventoryManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Reserve stock for all cart items.
     * Returns array of failures if any item has insufficient stock.
     */
    public function reserve(array $cartItems): array {
        $failures = [];
        foreach ($cartItems as $item) {
            $med = $this->db->prepare("SELECT id, name, stock FROM medicines WHERE id = ? FOR UPDATE");
            $med->execute([$item['medicine_id']]);
            $medicine = $med->fetch();

            if (!$medicine || $medicine['stock'] < $item['quantity']) {
                $failures[] = [
                    'medicine_id' => $item['medicine_id'],
                    'medicine_name' => $medicine['name'] ?? 'Unknown',
                    'available' => $medicine['stock'] ?? 0,
                    'requested' => $item['quantity']
                ];
            }
        }
        return $failures;
    }

    /**
     * Deduct stock after successful order placement.
     */
    public function deductStock(array $cartItems): void {
        foreach ($cartItems as $item) {
            $this->db->prepare("UPDATE medicines SET stock = stock - ? WHERE id = ?")
                     ->execute([$item['quantity'], $item['medicine_id']]);
        }
    }

    /**
     * Restore stock on order cancellation.
     */
    public function restoreStock(int $orderId): void {
        $items = $this->db->prepare("SELECT medicine_id, quantity FROM order_items WHERE order_id = ?");
        $items->execute([$orderId]);
        foreach ($items->fetchAll() as $item) {
            $this->db->prepare("UPDATE medicines SET stock = stock + ? WHERE id = ?")
                     ->execute([$item['quantity'], $item['medicine_id']]);
        }
    }

    /**
     * Audit logic: Reconcile stock nodes with physical reality (Simulation)
     */
    public function reconcileIntegrity(): int {
        // Logic parity: Clean up items where stock is negative (safety net)
        $this->db->query("UPDATE medicines SET stock = 0 WHERE stock < 0");
        return (int)$this->db->query("SELECT COUNT(*) FROM medicines WHERE stock < 10")->fetchColumn();
    }
}
