<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\Cart;
use App\Models\Medicine;
use App\Services\MedicineIntelligence;
use App\Middleware\AuthGuard;
use Exception;

/**
 * Cart Controller — Aligned with Unified Model Architecture
 */
class CartController extends BaseController {
    
    private $cartModel;
    private $medicineModel;

    public function __construct() {
        $this->cartModel = new Cart();
        $this->medicineModel = new Medicine();
    }

    /**
     * GET /cart
     */
    public function index() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $items = $this->cartModel->getForUser($user['id']);
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        return ResponseHandler::success([
            'items' => $items,
            'subtotal' => $subtotal,
            'delivery_fee' => $subtotal > 1000 ? 0 : 25,
            'total' => $subtotal + ($subtotal > 1000 ? 0 : 25)
        ]);
    }

    /**
     * POST /cart/add
     */
    public function addItem() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        
        $medicineId = (int)($data['medicine_id'] ?? 0);
        $qty = (int)($data['qty'] ?? 1);

        if (!$medicineId) return ResponseHandler::badRequest('Medicine ID required.');

        $medicine = $this->medicineModel->findById($medicineId);
        if (!$medicine) return ResponseHandler::notFound('Medicine artifact missing.');

        // AI Interaction Check
        $currentItems = $this->cartModel->getForUser($user['id']);
        $currentIds = array_column($currentItems, 'medicine_id');
        $intel = new MedicineIntelligence();
        $hazard = $intel->checkInteractions($medicineId, $currentIds);

        $this->cartModel->add($user['id'], $medicineId, $qty);

        return ResponseHandler::success([
            'hazard_warning' => $hazard,
            'medicine' => $medicine['name']
        ], $hazard ? "Medicine added with AI SAFETY WARNING." : "Medicine added to cart.");
    }

    /**
     * PUT /cart/{id}
     */
    public function updateItem($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $qty = (int)($data['qty'] ?? 1);

        $item = $this->cartModel->findById($id);
        if (!$item || $item['user_id'] != $user['id']) return ResponseHandler::forbidden();

        $this->cartModel->update($id, ['quantity' => $qty]);
        return ResponseHandler::success([], "Quantity synchronized.");
    }

    /**
     * DELETE /cart/{id}
     */
    public function removeItem($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        $item = $this->cartModel->findById($id);
        if (!$item || $item['user_id'] != $user['id']) return ResponseHandler::forbidden();

        $this->cartModel->delete($id);
        return ResponseHandler::success([], "Item decoupled from cart.");
    }

    /**
     * POST /cart/clear
     */
    public function clear() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $this->cartModel->clear($user['id']);
        return ResponseHandler::success([], "Cart node purged.");
    }
}
