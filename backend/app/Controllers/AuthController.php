<?php
namespace App\Controllers;

use App\Models\User;
use App\Middleware\AuthGuard;

/**
 * Auth Controller
 * Handles user authentication, registration, and session management.
 */
class AuthController extends BaseController {

    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        $data = $this->getPostData();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->json(['success' => false, 'message' => 'Email and password are required'], 400);
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->json(['success' => false, 'message' => 'Invalid email or password'], 401);
        }

        // Check user status
        if ($user['status'] === 'inactive') {
            return $this->json(['success' => false, 'message' => 'Your account is pending verification. Please wait for admin approval.'], 403);
        }

        if ($user['status'] === 'blocked') {
            return $this->json(['success' => false, 'message' => 'Your account has been suspended. Please contact support.'], 403);
        }

        // Generate a real HMAC-SHA256 JWT
        $config = require __DIR__ . '/../../config/app.php';
        $secret = $config['jwt_secret'];
        $expiry = time() + ($config['jwt_expiry'] ?? 86400);

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['id' => $user['id'], 'role' => $user['role'], 'exp' => $expiry]);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        $token = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return $this->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }

    public function register() {
        $data = $this->getPostData();
        
        // Basic validation
        if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            return $this->json(['success' => false, 'message' => 'Missing required fields'], 400);
        }

        // Age Validation (Mandatory for Vendor and Delivery)
        if (!empty($data['dob'])) {
            $dob = new \DateTime($data['dob']);
            $now = new \DateTime();
            $age = $now->diff($dob)->y;
            if ($age < 18 && ($data['role'] === 'vendor' || $data['role'] === 'delivery')) {
                return $this->json(['success' => false, 'message' => 'Clinical Safety Alert: Minimum age of 18 required for ' . ucfirst($data['role']) . ' accounts.'], 403);
            }
        }

        // Check if user exists
        if ($this->userModel->findByEmail($data['email'])) {
            return $this->json(['success' => false, 'message' => 'Email already registered in the grid.'], 409);
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            // Hash password
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            
            // Create user
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
                'role' => $data['role'],
                'phone' => $data['phone'] ?? null,
                'status' => ($data['role'] === 'user') ? 'active' : 'inactive'
            ];

            // Store lat/lng for map pinpoint (Common for User and Vendor now)
            $userData['latitude'] = !empty($data['latitude']) ? (float)$data['latitude'] : null;
            $userData['longitude'] = !empty($data['longitude']) ? (float)$data['longitude'] : null;

            $userId = $this->userModel->create($userData);

            if (!$userId) throw new \Exception("Failed to create user node.");

            // Role-specific Profile Creation
            if ($data['role'] === 'user') {
                $db->prepare("INSERT INTO user_profiles (user_id, dob, gender) VALUES (?, ?, ?)")
                   ->execute([$userId, $data['dob'] ?? null, $data['gender'] ?? null]);
                
                // Add Address if provided
                if (!empty($data['address'])) {
                    $db->prepare("INSERT INTO addresses (user_id, label, address_line1, city, pincode, latitude, longitude) VALUES (?, 'Home', ?, ?, ?, ?, ?)")
                       ->execute([
                           $userId, 
                           $data['address'], 
                           $data['city'] ?? 'Pune', 
                           $data['pincode'] ?? null,
                           $userData['latitude'],
                           $userData['longitude']
                       ]);
                }
            } 
            else if ($data['role'] === 'vendor') {
                $db->prepare("INSERT INTO vendor_profiles (user_id, dob, pharmacy_name, owner_name, license_number, pharmacist_reg_number, pan_card_number, aadhaar_number, gst_number, address, bank_name, account_number, ifsc_code, opening_time, closing_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                   ->execute([
                       $userId, 
                       $data['dob'] ?? null,
                       $data['pharmacy_name'] ?? $data['name'], 
                       $data['owner_name'] ?? $data['name'],
                       $data['license_number'] ?? null,
                       $data['pharmacist_reg_number'] ?? null,
                       $data['pan_card_number'] ?? null,
                       $data['aadhaar_number'] ?? null,
                       $data['gst_number'] ?? null,
                       $data['address'] ?? null,
                       $data['bank_name'] ?? null,
                       $data['account_number'] ?? null,
                       $data['ifsc_code'] ?? null,
                       $data['opening_time'] ?? '09:00:00',
                       $data['closing_time'] ?? '21:00:00'
                   ]);
            }
            else if ($data['role'] === 'delivery') {
                $db->prepare("INSERT INTO delivery_profiles (user_id, dob, vehicle_type, vehicle_number, zone, license_number, aadhaar_number, pan_number, bank_name, account_number, ifsc_code, current_address, permanent_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                   ->execute([
                       $userId,
                       $data['dob'] ?? null,
                       $data['vehicle_type'] ?? 'bike',
                       $data['vehicle_number'] ?? null,
                       $data['zone'] ?? 'Pune Central',
                       $data['license_number'] ?? null,
                       $data['aadhaar_number'] ?? null,
                       $data['pan_number'] ?? null,
                       $data['bank_name'] ?? null,
                       $data['account_number'] ?? null,
                       $data['ifsc_code'] ?? null,
                       $data['current_address'] ?? null,
                       $data['permanent_address'] ?? null
                   ]);
            }

            // Handle File Uploads (License, Selfie, ID Proof)
            $uploadDir = __DIR__ . '/../../storage/uploads/kyc/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // 1. License Copy
            if (isset($_FILES['license_copy']) && $_FILES['license_copy']['error'] === UPLOAD_ERR_OK) {
                $fileName = 'license_' . $userId . '_' . time() . '.' . pathinfo($_FILES['license_copy']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['license_copy']['tmp_name'], $uploadDir . $fileName)) {
                    $docType = ($data['role'] === 'vendor') ? 'pharmacy_license' : 'license';
                    $db->prepare("INSERT INTO kyc_documents (user_id, document_type, document_image, status) VALUES (?, ?, ?, 'pending')")
                       ->execute([$userId, $docType, 'storage/uploads/kyc/' . $fileName]);
                }
            }

            // 2. Owner/Rider ID Proof (Aadhaar/PAN)
            if (isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] === UPLOAD_ERR_OK) {
                $fileName = 'id_' . $userId . '_' . time() . '.' . pathinfo($_FILES['id_proof']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['id_proof']['tmp_name'], $uploadDir . $fileName)) {
                    $db->prepare("INSERT INTO kyc_documents (user_id, document_type, document_image, status) VALUES (?, 'aadhar', ?, 'pending')")
                       ->execute([$userId, 'storage/uploads/kyc/' . $fileName]);
                }
            }

            // 3. Selfie Photo (Delivery Only)
            if ($data['role'] === 'delivery' && isset($_FILES['selfie']) && $_FILES['selfie']['error'] === UPLOAD_ERR_OK) {
                $fileName = 'selfie_' . $userId . '_' . time() . '.' . pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['selfie']['tmp_name'], $uploadDir . $fileName)) {
                    $db->prepare("UPDATE delivery_profiles SET selfie_image = ? WHERE user_id = ?")
                       ->execute(['storage/uploads/kyc/' . $fileName, $userId]);
                }
            }

            $db->commit();
            return $this->json(['success' => true, 'message' => 'Node established successfully. Access granted.', 'user_id' => $userId], 201);

        } catch (\Exception $e) {
            $db->rollBack();
            return $this->json(['success' => false, 'message' => 'Synchronization failed: ' . $e->getMessage()], 500);
        }
    }

    public function me() {
        $userData = get_current_user_data();
        if (!$userData) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Fetch full user details from DB
        $user = $this->userModel->findById($userData['id']);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User node not found'], 404);
        }

        return $this->json([
            'success' => true, 
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }

    public function logout() {
        // Revoke the token by adding it to the session blacklist
        // This prevents the token from being used after logout
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if ($auth && strpos($auth, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $auth);
            $parts = explode('.', $token);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode($parts[1]), true);
                $expiry = $payload['exp'] ?? (time() + 86400);
                
                (new \App\Models\RevokedToken())->revoke(hash('sha256', $token), $expiry);
            }
        }
        return $this->json(['success' => true, 'message' => 'Logged out successfully. Token revoked.']);
    }

    public function forgotPassword() {
        $data = $this->getPostData();
        $email = $data['email'] ?? '';
        
        if (empty($email)) return $this->json(['success' => false, 'message' => 'Email required'], 400);
        
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            // Security: Always return success to prevent email enumeration
            return $this->json(['success' => true, 'message' => 'If this email exists, a clinical recovery link has been dispatched.']);
        }

        $resetModel = new \App\Models\PasswordReset();
        $token = $resetModel->createToken($email);

        if ($token) {
            // In production, dispatch email here via NotificationService
            // For now, we return the token in the response for demo/debug purposes if env is dev
            $debug = getenv('APP_DEBUG') === 'true';
            return $this->json([
                'success' => true, 
                'message' => 'Clinical account recovery link dispatched to ' . $email,
                'debug_token' => $debug ? $token : null
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Failed to initialize recovery protocol.'], 500);
    }

    public function resetPassword() {
        $data = $this->getPostData();
        $token = $data['token'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($token) || empty($password)) {
            return $this->json(['success' => false, 'message' => 'Security token and new credentials required'], 400);
        }

        $resetModel = new \App\Models\PasswordReset();
        $email = $resetModel->verifyToken($token);

        if (!$email) {
            return $this->json(['success' => false, 'message' => 'Security token invalid or expired. Access denied.'], 403);
        }

        // Update password
        $user = $this->userModel->findByEmail($email);
        if ($user) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $this->userModel->update($user['id'], ['password' => $hashedPassword]);
            $resetModel->deleteToken($token);
            
            return $this->json(['success' => true, 'message' => 'Clinical credentials synchronized successfully.']);
        }

        return $this->json(['success' => false, 'message' => 'Account node not found.'], 404);
    }

    /**
     * Testing Helper: Generate a token for a given user data set
     */
    public function testGenerateToken($userData) {
        $config = require __DIR__ . '/../../config/app.php';
        $secret = $config['jwt_secret'];
        $expiry = time() + ($config['jwt_expiry'] ?? 86400);

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['id' => $userData['id'], 'role' => $userData['role'], 'exp' => $expiry]);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
