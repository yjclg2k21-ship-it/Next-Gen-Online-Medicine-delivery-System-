<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\WalletTransaction;
use App\Models\Wishlist;
use App\Models\ReturnRequest;
use App\Models\Notification;
use App\Middleware\AuthGuard;
use App\Compliance\AuditLogger;
use App\Services\MedicineIntelligence;
use Exception;

/**
 * User Controller — Aligned with Unified Model Architecture
 */
class UserController extends BaseController {

    private $userModel;
    private $orderModel;
    private $cartModel;
    private $addressModel;
    private $walletModel;
    private $wishlistModel;
    private $returnModel;
    private $notificationModel;

    public function __construct() {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->addressModel = new Address();
        $this->walletModel = new WalletTransaction();
        $this->wishlistModel = new Wishlist();
        $this->returnModel = new ReturnRequest();
        $this->notificationModel = new Notification();
    }

    /**
     * GET /user/profile
     */
    public function profile() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->userModel->getUserProfile($user['id']);
        if (!$data) return ResponseHandler::notFound('User profile not found.');
        
        // Sanitize
        unset($data['password']);
        return ResponseHandler::success(['user' => $data]);
    }

    /**
     * POST /user/profile/update
     */
    public function updateProfile() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        $update = [];
        if (!empty($data['name']))  $update['name'] = $data['name'];
        if (!empty($data['phone'])) $update['phone'] = $data['phone'];

        $db = \App\Core\Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            // Update base user info
            if (!empty($update)) {
                $this->userModel->update($user['id'], $update);
            }

            // Update role-specific profile
            $role = $user['role'] ?? User::ROLE_USER;
            if ($role === User::ROLE_USER) {
                $profileUpdate = [];
                $fields = ['dob', 'gender', 'blood_group', 'emergency_contact'];
                foreach ($fields as $f) {
                    if (isset($data[$f])) $profileUpdate[$f] = $data[$f];
                }
                
                if (!empty($profileUpdate)) {
                    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM user_profiles WHERE user_id = ?");
                    $stmtCheck->execute([$user['id']]);
                    if ($stmtCheck->fetchColumn() > 0) {
                        $set = implode(' = ?, ', array_keys($profileUpdate)) . ' = ?';
                        $stmt = $db->prepare("UPDATE user_profiles SET {$set} WHERE user_id = ?");
                        $stmt->execute([...array_values($profileUpdate), $user['id']]);
                    } else {
                        $cols = implode(', ', array_keys($profileUpdate)) . ', user_id';
                        $placeholders = implode(', ', array_fill(0, count($profileUpdate), '?')) . ', ?';
                        $stmt = $db->prepare("INSERT INTO user_profiles ({$cols}) VALUES ({$placeholders})");
                        $stmt->execute([...array_values($profileUpdate), $user['id']]);
                    }
                }
            } elseif ($role === User::ROLE_VENDOR) {
                $profileUpdate = [];
                $fields = ['pharmacy_name', 'owner_name', 'license_number', 'pharmacist_reg_number', 'pan_card_number', 'aadhaar_number', 'gst_number', 'address', 'opening_time', 'closing_time', 'bank_name', 'account_number', 'ifsc_code', 'latitude', 'longitude', 'delivery_radius_km'];
                foreach ($fields as $f) {
                    if (isset($data[$f])) $profileUpdate[$f] = $data[$f];
                }

                if (!empty($profileUpdate)) {
                    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM vendor_profiles WHERE user_id = ?");
                    $stmtCheck->execute([$user['id']]);
                    if ($stmtCheck->fetchColumn() > 0) {
                        $set = implode(' = ?, ', array_keys($profileUpdate)) . ' = ?';
                        $stmt = $db->prepare("UPDATE vendor_profiles SET {$set} WHERE user_id = ?");
                        $stmt->execute([...array_values($profileUpdate), $user['id']]);
                    } else {
                        $cols = implode(', ', array_keys($profileUpdate)) . ', user_id';
                        $placeholders = implode(', ', array_fill(0, count($profileUpdate), '?')) . ', ?';
                        $stmt = $db->prepare("INSERT INTO vendor_profiles ({$cols}) VALUES ({$placeholders})");
                        $stmt->execute([...array_values($profileUpdate), $user['id']]);
                    }
                }
            } elseif ($role === User::ROLE_DELIVERY) {
                $profileUpdate = [];
                $fields = ['vehicle_type', 'vehicle_number', 'zone', 'license_number', 'aadhaar_number', 'pan_number', 'bank_name', 'account_number', 'ifsc_code', 'current_address', 'permanent_address'];
                foreach ($fields as $f) {
                    if (isset($data[$f])) $profileUpdate[$f] = $data[$f];
                }

                if (!empty($profileUpdate)) {
                    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM delivery_profiles WHERE user_id = ?");
                    $stmtCheck->execute([$user['id']]);
                    if ($stmtCheck->fetchColumn() > 0) {
                        $set = implode(' = ?, ', array_keys($profileUpdate)) . ' = ?';
                        $stmt = $db->prepare("UPDATE delivery_profiles SET {$set} WHERE user_id = ?");
                        $stmt->execute([...array_values($profileUpdate), $user['id']]);
                    } else {
                        $cols = implode(', ', array_keys($profileUpdate)) . ', user_id';
                        $placeholders = implode(', ', array_fill(0, count($profileUpdate), '?')) . ', ?';
                        $stmt = $db->prepare("INSERT INTO delivery_profiles ({$cols}) VALUES ({$placeholders})");
                        $stmt->execute([...array_values($profileUpdate), $user['id']]);
                    }
                }
            }

            $db->commit();
            return ResponseHandler::success([], 'Profile synchronized across nodes.');
        } catch (Exception $e) {
            $db->rollBack();
            return ResponseHandler::error('Fulfillment error: Profile synchronization failed.');
        }
    }

    /**
     * GET /user/addresses
     */
    public function addresses() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $addresses = $this->addressModel->getByUser($user['id']);
        return ResponseHandler::success(['addresses' => $addresses]);
    }

    /**
     * POST /user/addresses
     */
    public function addAddress() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        if (empty($data['address_line1'])) return ResponseHandler::badRequest('Fulfillment error: Address line 1 required.');

        $latitude = !empty($data['latitude'])  ? (float)$data['latitude']  : null;
        $longitude = !empty($data['longitude']) ? (float)$data['longitude'] : null;
        
        // Pincode-based coordinates fallback
        if ((empty($latitude) || empty($longitude)) && !empty($data['pincode'])) {
            $pincodeDefaults = [
                '415523' => [17.99031790, 74.43031892],
                '415528' => [17.92570000, 74.58330000],
                '415521' => [18.04100000, 74.20850000],
                '415537' => [18.02000000, 74.47000000],
                '415509' => [17.90560000, 74.19720000],
            ];
            $pin = $data['pincode'];
            if (isset($pincodeDefaults[$pin])) {
                $latitude = $pincodeDefaults[$pin][0];
                $longitude = $pincodeDefaults[$pin][1];
            }
        }

        $id = $this->addressModel->create([
            'user_id'      => $user['id'],
            'label'        => $data['label'] ?? 'Home',
            'address_line1'=> $data['address_line1'],
            'address_line2'=> $data['address_line2'] ?? null,
            'city'         => $data['city'] ?? null,
            'state'        => $data['state'] ?? null,
            'pincode'      => $data['pincode'] ?? null,
            'latitude'     => $latitude,
            'longitude'    => $longitude,
            'is_default'   => !empty($data['is_default']) ? 1 : 0
        ]);

        if (!empty($data['is_default'])) {
            $this->addressModel->setDefault($user['id'], $id);
        }

        return ResponseHandler::success(['id' => $id], 'Address artifact injected.', 201);
    }

    /**
     * DELETE /user/addresses/{id}
     */
    public function deleteAddress($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        $address = $this->addressModel->findById($id);
        if (!$address || $address['user_id'] != $user['id']) {
            return ResponseHandler::forbidden('Access denied to address node.');
        }

        $this->addressModel->delete($id);
        return ResponseHandler::success([], 'Address node decoupled.');
    }

    /**
     * GET /user/wallet
     */
    public function walletBalance() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $full = $this->userModel->findById($user['id']);
        $txns = $this->walletModel->getByUser($user['id']);

        return ResponseHandler::success([
            'balance' => (float)($full['wallet_balance'] ?? 0),
            'currency' => 'INR',
            'recent_history' => array_slice($txns, 0, 15)
        ], 'Financial ledger synchronized.');
    }

    /**
     * GET /user/wishlist
     */
    public function wishlist() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $items = $this->wishlistModel->getByUser($user['id']);
        return ResponseHandler::success(['wishlist' => $items]);
    }

    /**
     * POST /user/wishlist/toggle
     */
    public function toggleWishlist() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $medId = (int)($data['medicine_id'] ?? 0);

        if (!$medId) return ResponseHandler::badRequest('Medicine ID required.');

        $status = $this->wishlistModel->toggle($user['id'], $medId);
        return ResponseHandler::success(['added' => $status], 'Wishlist adjacency modified.');
    }

    /**
     * GET /user/notifications
     */
    public function notifications() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        $notifs = $this->notificationModel->findAll();
        $notifs = array_filter($notifs, fn($n) => $n['user_id'] == $user['id']);
        
        // Mark as read logic
        $db = \App\Core\Database::getInstance()->getConnection();
        $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0")->execute([$user['id']]);

        return ResponseHandler::success(['notifications' => array_values($notifs)]);
    }

    /**
     * GET /user/returns
     */
    public function returns() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $returns = $this->returnModel->getByUser($user['id']);
        return ResponseHandler::success(['returns' => $returns]);
    }

    /**
     * GET /user/dashboard
     */
    public function dashboard() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        $stats = [
            'total_orders' => $this->orderModel->count("user_id = ?", [$user['id']]),
            'active_orders' => $this->orderModel->count("user_id = ? AND status NOT IN ('delivered', 'cancelled', 'return_requested', 'returned')", [$user['id']]),
            'total_spent' => $this->orderModel->sum('grand_total', "user_id = ? AND status = 'delivered'", [$user['id']])
        ];

        return ResponseHandler::success([
            'stats' => $stats,
            'recent_orders' => $this->orderModel->getByUserId($user['id']),
            'unread_notifs' => $this->notificationModel->count("user_id = ? AND is_read = 0", [$user['id']])
        ], 'User telemetry synchronized.');
    }

    /**
     * PUT /user/addresses/{id}
     */
    public function updateAddress($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        $address = $this->addressModel->findById($id);
        if (!$address || $address['user_id'] != $user['id']) {
            return ResponseHandler::forbidden();
        }

        $latitude = !empty($data['latitude'])  ? (float)$data['latitude']  : ($address['latitude']  ?? null);
        $longitude = !empty($data['longitude']) ? (float)$data['longitude'] : ($address['longitude'] ?? null);
        $pincode = $data['pincode'] ?? $address['pincode'];

        // Pincode-based coordinates fallback
        if ((empty($latitude) || empty($longitude)) && !empty($pincode)) {
            $pincodeDefaults = [
                '415523' => [17.99031790, 74.43031892],
                '415528' => [17.92570000, 74.58330000],
                '415521' => [18.04100000, 74.20850000],
                '415537' => [18.02000000, 74.47000000],
                '415509' => [17.90560000, 74.19720000],
            ];
            if (isset($pincodeDefaults[$pincode])) {
                $latitude = $pincodeDefaults[$pincode][0];
                $longitude = $pincodeDefaults[$pincode][1];
            }
        }

        $this->addressModel->update($id, [
            'label'         => $data['label']         ?? $address['label'],
            'address_line1' => $data['address_line1'] ?? $address['address_line1'],
            'address_line2' => $data['address_line2'] ?? $address['address_line2'],
            'city'          => $data['city']          ?? $address['city'],
            'pincode'       => $pincode,
            'latitude'      => $latitude,
            'longitude'     => $longitude,
        ]);

        return ResponseHandler::success([], 'Address updated.');
    }

    /**
     * PUT /user/addresses/{id}/default
     */
    public function setDefaultAddress($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        $success = $this->addressModel->setDefault($user['id'], $id);
        if (!$success) return ResponseHandler::error('Failed to update primary node.');

        return ResponseHandler::success([], 'Primary address synchronized.');
    }

    /**
     * GET /user/payments
     */
    public function payments() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $history = (new \App\Models\Payment())->getHistory($user['id']);
        return ResponseHandler::success(['payments' => $history]);
    }

    /**
     * GET /user/recommendations
     * Uses ML model if available, falls back to rule-based
     */
    public function recommendations() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        // Try ML recommendation service first
        try {
            $ch = curl_init('http://localhost:5050/recommend');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode(['user_id' => $user['id'], 'limit' => 12]),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_CONNECTTIMEOUT => 1
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $mlResult = json_decode($response, true);
                if ($mlResult && $mlResult['success'] && !empty($mlResult['recommendations'])) {
                    // Enrich ML recommendations with category names and brand info
                    $db = \App\Core\Database::getInstance()->getConnection();
                    $recs = $mlResult['recommendations'];
                    foreach ($recs as &$rec) {
                        $stmt = $db->prepare("SELECT m.*, c.name as category_name, b.name as brand_name, b.type as brand_type 
                                              FROM medicines m LEFT JOIN categories c ON m.category_id = c.id 
                                              LEFT JOIN brands b ON m.brand_id = b.id WHERE m.id = ?");
                        $stmt->execute([$rec['id']]);
                        $full = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($full) {
                            $rec = array_merge($rec, $full);
                            $rec['reason'] = $rec['reason'] ?? 'ml_recommended';
                            $rec['score'] = (int)(($rec['ml_score'] ?? 0.5) * 100);
                        }
                    }
                    
                    return ResponseHandler::success([
                        'recommendations' => $recs,
                        'primary_focus' => 'ML Personalized',
                        'insight_summary' => 'AI-powered recommendations based on your purchase patterns and medicine similarity analysis.',
                        'algorithm' => 'ml_collaborative_content',
                        'factors' => ['purchase_history', 'user_similarity', 'medicine_features', 'collaborative_filtering']
                    ]);
                }
            }
        } catch (\Exception $e) {
            // ML service not available — fall through
        }

        // Fallback: Rule-based recommendations
        $intel = new MedicineIntelligence();
        $data = $intel->getRecommendations($user['id']);
        return ResponseHandler::success($data);
    }

    /**
     * POST /user/change-password
     */
    public function changePassword() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        $oldPassword = $data['old_password'] ?? $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword)) {
            return ResponseHandler::badRequest('Current and new credentials required.');
        }

        $currentUser = $this->userModel->findById($user['id']);
        if (!password_verify($oldPassword, $currentUser['password'])) {
            return ResponseHandler::error('Current credential mismatch. Adjacency rotation blocked.', 401);
        }

        $this->userModel->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);

        return ResponseHandler::success([], 'Security credentials rotated successfully.');
    }

    /**
     * DELETE /user/account
     */
    public function deleteAccount() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        if (empty($data['password'])) return ResponseHandler::badRequest('Credential confirmation required for decommissioning.');

        $currentUser = $this->userModel->findById($user['id']);
        if (!password_verify($data['password'], $currentUser['password'])) {
            return ResponseHandler::error('Credential mismatch. Decommissioning aborted.', 401);
        }

        $this->userModel->delete($user['id']);
        return ResponseHandler::success([], 'User node decommissioned.');
    }

    /**
     * POST /user/deactivate
     */
    public function deactivateAccount() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        $this->userModel->update($user['id'], ['status' => 'inactive']);
        return ResponseHandler::success([], 'User node hibernated.');
    }
}
