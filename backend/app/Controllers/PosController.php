<?php
namespace App\Controllers;

use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Middleware\AuthGuard;
use Exception;

/**
 * POS (Point of Sale) Controller
 * Handles over-the-counter sales for pharmacy vendors.
 */
class PosController extends BaseController {

    private $saleModel;
    private $itemModel;

    public function __construct() {
        $this->saleModel = new PosSale();
        $this->itemModel = new PosSaleItem();
    }

    private function getVendorId() {
        AuthGuard::handle();
        return AuthGuard::getUser()['id'];
    }

    public function createSale() {
        try {
            $vendorId = $this->getVendorId();
            $data = $this->getPostData();
            $items = $data['items'] ?? [];
            $total = (float)($data['total_amount'] ?? 0);
            $paymentMethod = $data['payment_method'] ?? 'cash';

            if (empty($items)) return $this->json(['success' => false, 'message' => 'items required'], 400);

            // Using transaction from Database instance via model
            $db = \App\Core\Database::getInstance()->getConnection();
            $db->beginTransaction();

            $saleId = $this->saleModel->create([
                'vendor_id' => $vendorId,
                'total_amount' => $total,
                'payment_method' => $paymentMethod
            ]);

            foreach ($items as $item) {
                $this->itemModel->create([
                    'pos_sale_id' => $saleId, 
                    'medicine_id' => $item['medicine_id'], 
                    'quantity' => $item['quantity'], 
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price']
                ]);

                // Decrement stock in database to reflect the OTC sale
                $stmtUpdate = $db->prepare("UPDATE medicines SET stock = GREATEST(0, stock - ?) WHERE id = ?");
                $stmtUpdate->execute([$item['quantity'], $item['medicine_id']]);
            }

            $db->commit();
            return $this->json(['success' => true, 'message' => 'Sale processed successfully', 'sale_id' => $saleId], 201);
        } catch (Exception $e) {
            $db = \App\Core\Database::getInstance()->getConnection();
            if ($db->inTransaction()) $db->rollBack();
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSales() {
        try {
            $vendorId = $this->getVendorId();
            $sales = $this->saleModel->getByVendor($vendorId);
            return $this->json(['sales' => $sales]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function close() {
        return $this->json(['success' => true, 'message' => 'POS session closed.']);
    }
}
