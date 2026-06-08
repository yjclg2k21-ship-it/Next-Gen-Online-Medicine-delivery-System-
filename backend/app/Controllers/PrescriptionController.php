<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Models\Prescription;
use App\Models\Cart;
use App\Models\Medicine;
use App\Middleware\AuthGuard;
use App\Middleware\RoleCheck;
use App\Compliance\AuditLogger;
use App\Compliance\PrescriptionVerifier;
use Exception;

/**
 * Prescription Controller — Enhanced Workflow
 * Features: Expiry validation, auto-link medicines to cart, reuse prevention
 */
class PrescriptionController extends BaseController {
    
    private $prescriptionModel;

    public function __construct() {
        $this->prescriptionModel = new Prescription();
    }

    /**
     * GET /prescriptions
     */
    public function index() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Check if new columns exist (migration applied?)
        $hasNewColumns = false;
        try {
            $colCheck = $db->query("SHOW COLUMNS FROM prescriptions LIKE 'reviewed_by'")->fetch();
            $hasNewColumns = (bool)$colCheck;
        } catch (\Exception $e) {}

        if ($user['role'] === 'admin') {
            $stmt = $db->query("SELECT p.*, u.name as patient_name FROM prescriptions p JOIN users u ON p.user_id = u.id WHERE p.deleted_at IS NULL ORDER BY p.created_at DESC");
            $prescriptions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else if ($user['role'] === 'vendor') {
            if ($hasNewColumns) {
                $stmt = $db->prepare("SELECT p.*, u.name as patient_name FROM prescriptions p JOIN users u ON p.user_id = u.id WHERE p.deleted_at IS NULL AND (p.status = 'pending' OR p.reviewed_by = ?) ORDER BY p.created_at DESC");
                $stmt->execute([(int)$user['id']]);
            } else {
                $stmt = $db->query("SELECT p.*, u.name as patient_name FROM prescriptions p JOIN users u ON p.user_id = u.id WHERE p.deleted_at IS NULL AND p.status = 'pending' ORDER BY p.created_at DESC");
            }
            $prescriptions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $prescriptions = $this->prescriptionModel->getByUserId($user['id']);
        }

        // Enrich with expiry status (safe even without new columns)
        foreach ($prescriptions as &$rx) {
            $rx['is_expired'] = $this->isExpired($rx);
            $rx['is_reusable'] = ($rx['times_used'] ?? 0) < ($rx['max_uses'] ?? 1);
            
            // Only fetch linked medicines if table exists
            try {
                $rx['linked_medicines'] = $this->prescriptionModel->getLinkedMedicines($rx['id']);
            } catch (\Exception $e) {
                $rx['linked_medicines'] = [];
            }
        }
        
        return ResponseHandler::success(['prescriptions' => $prescriptions]);
    }

    /**
     * POST /prescriptions/upload
     */
    public function upload() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $fileData = $_FILES['prescription'] ?? null;
        
        if (!$fileData) return ResponseHandler::badRequest('No clinical document provided.');

        // Securely store file
        $fileName = 'rx_' . $user['id'] . '_' . time() . '_' . uniqid() . '.' . pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $targetDir = __DIR__ . '/../../storage/private/prescriptions/';
        
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $targetPath = $targetDir . $fileName;
        
        $uploadSuccess = PHP_SAPI === 'cli' ? copy($fileData['tmp_name'], $targetPath) : move_uploaded_file($fileData['tmp_name'], $targetPath);
        if ($uploadSuccess) {
            $expiryDate = $_POST['expiry_date'] ?? null;
            $issuedDate = $_POST['issued_date'] ?? null;

            // Default expiry: 180 days from issue/upload if not specified
            if (!$expiryDate) {
                $baseDate = $issuedDate ? strtotime($issuedDate) : time();
                $expiryDate = date('Y-m-d', $baseDate + (180 * 86400));
            }

            // Find nearest vendor based on delivery coordinates, home address, or default address
            $nearestVendorId = 2; // Default fallback
            try {
                $db = \App\Core\Database::getInstance()->getConnection();
                
                // Prioritize user's saved 'home' address coordinates
                $addrStmt = $db->prepare("SELECT latitude, longitude FROM addresses WHERE user_id = ? AND LOWER(label) = 'home' LIMIT 1");
                $addrStmt->execute([$user['id']]);
                $addr = $addrStmt->fetch();

                if ($addr && $addr['latitude'] && $addr['longitude']) {
                    $deliveryLat = (float)$addr['latitude'];
                    $deliveryLng = (float)$addr['longitude'];
                } else {
                    // Fallback to POST coordinates
                    $deliveryLat = isset($_POST['latitude']) ? (float)$_POST['latitude'] : null;
                    $deliveryLng = isset($_POST['longitude']) ? (float)$_POST['longitude'] : null;
                }
                
                if ($deliveryLat && $deliveryLng) {
                    $stmt = $db->query("SELECT user_id, latitude, longitude FROM vendor_profiles WHERE latitude IS NOT NULL AND longitude IS NOT NULL");
                    $vendors = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    if (!empty($vendors)) {
                        $minDistance = INF;
                        $earthRadius = 6371;
                        foreach ($vendors as $v) {
                            $dLat = deg2rad($deliveryLat - (float)$v['latitude']);
                            $dLng = deg2rad($deliveryLng - (float)$v['longitude']);
                            $a = sin($dLat / 2) ** 2
                                + cos(deg2rad((float)$v['latitude'])) * cos(deg2rad($deliveryLat))
                                * sin($dLng / 2) ** 2;
                            $distance = $earthRadius * 2 * asin(sqrt($a));
                            if ($distance < $minDistance) {
                                $minDistance = $distance;
                                $nearestVendorId = (int)$v['user_id'];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Keep default vendor if address or DB fails
            }

            // Build create payload
            $createData = [
                'user_id' => $user['id'],
                'image_path' => $fileName,
                'doctor_name' => $_POST['doctor_name'] ?? 'Clinical Practitioner',
                'status' => 'pending',
                'vendor_id' => $nearestVendorId
            ];

            // Add new fields only if migration has been applied
            try {
                $db = \App\Core\Database::getInstance()->getConnection();
                $cols = $db->query("SHOW COLUMNS FROM prescriptions LIKE 'expiry_date'")->fetch();
                if ($cols) {
                    $createData['issued_date'] = $issuedDate ?? date('Y-m-d');
                    $createData['expiry_date'] = $expiryDate;
                    $createData['times_used'] = 0;
                    $createData['max_uses'] = (int)($_POST['max_uses'] ?? 1);
                }
            } catch (\Exception $e) {
                // Migration not applied yet — continue with basic fields
            }

            $id = $this->prescriptionModel->create($createData);

            AuditLogger::prescriptionUploaded($user['id'], $id);
            return ResponseHandler::success(['id' => $id, 'expiry_date' => $expiryDate], 'Prescription uploaded. Pending clinical review.', 201);
        }

        return ResponseHandler::error('Failed to secure clinical document.', 500);
    }

    /**
     * GET /prescriptions/{id}/view
     */
    public function view($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        
        $prescription = $this->prescriptionModel->findById($id);
        if (!$prescription) return ResponseHandler::notFound('Clinical record missing.');
        
        if ($prescription['user_id'] != $user['id'] && !in_array($user['role'], ['vendor', 'admin'])) {
            return ResponseHandler::forbidden('Access to private medical record denied.');
        }

        $fileName = $prescription['image_path'] ?? $prescription['file_path'] ?? '';
        $filePath = __DIR__ . '/../../storage/private/prescriptions/' . $fileName;
        
        if (file_exists($filePath)) {
            // Clear any previously set headers (like global Content-Type: application/json)
            header_remove('Content-Type');
            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
            header('Content-Type: ' . $mimeType);
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        }

        return ResponseHandler::notFound('Document file not found on disk.');
    }

    /**
     * POST /prescriptions/{id}/approve
     * Enhanced: Auto-links medicines to user's cart upon approval
     */
    public function approve($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if (!in_array($user['role'], ['vendor', 'admin'])) {
            return ResponseHandler::forbidden('Only pharmacists can review prescriptions.');
        }

        $prescription = $this->prescriptionModel->findById($id);
        if (!$prescription) return ResponseHandler::notFound('Prescription not found.');

        // Check expiry before approving
        if ($this->isExpired($prescription)) {
            return ResponseHandler::badRequest('Cannot approve an expired prescription. Expiry: ' . ($prescription['expiry_date'] ?? 'N/A'));
        }
        
        $this->prescriptionModel->update($id, [
            'status' => 'approved',
            'reviewed_by' => $user['id'],
            'reviewed_at' => date('Y-m-d H:i:s')
        ]);

        // Auto-link: Add prescription medicines to user's cart
        $linkedMedicines = $this->autoLinkMedicinesToCart($id, $prescription['user_id']);

        AuditLogger::log($user['id'], 'prescription_approved', ['prescription_id' => $id]);

        return ResponseHandler::success([
            'auto_linked' => $linkedMedicines
        ], 'Prescription approved. ' . count($linkedMedicines) . ' medicine(s) auto-added to patient cart.');
    }

    /**
     * POST /prescriptions/{id}/reject
     */
    public function reject($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if (!in_array($user['role'], ['vendor', 'admin'])) {
            return ResponseHandler::forbidden('Only pharmacists can review prescriptions.');
        }
        
        $data = $this->getPostData();
        
        $this->prescriptionModel->update($id, [
            'status' => 'rejected',
            'reviewed_by' => $user['id'],
            'reviewed_at' => date('Y-m-d H:i:s'),
            'comment' => $data['remarks'] ?? $data['comment'] ?? 'Rejected by pharmacist.'
        ]);

        AuditLogger::log($user['id'], 'prescription_rejected', ['prescription_id' => $id]);

        return ResponseHandler::success([], 'Prescription rejected by pharmacist.');
    }

    /**
     * POST /prescriptions/{id}/link-medicines
     * Manually link detected medicines to a prescription (called after AI analysis)
     */
    public function linkMedicines($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();

        $prescription = $this->prescriptionModel->findById($id);
        if (!$prescription) return ResponseHandler::notFound('Prescription not found.');
        if ($prescription['user_id'] != $user['id'] && !in_array($user['role'], ['vendor', 'admin'])) {
            return ResponseHandler::forbidden();
        }

        $medicines = $data['medicines'] ?? [];
        if (empty($medicines)) return ResponseHandler::badRequest('No medicines provided.');

        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Check if prescription_medicines table exists
        try {
            $tableCheck = $db->query("SHOW TABLES LIKE 'prescription_medicines'")->fetch();
            if (!$tableCheck) {
                // Table doesn't exist yet — return success without linking
                return ResponseHandler::success(['linked' => [], 'note' => 'Migration pending. Medicines noted but not persisted.'], 'Link acknowledged (migration required).');
            }
        } catch (\Exception $e) {
            return ResponseHandler::success(['linked' => []], 'Link acknowledged.');
        }

        $linked = [];

        foreach ($medicines as $med) {
            $medicineName = $med['name'] ?? '';
            $medicineId = $med['medicine_id'] ?? null;
            $dosage = $med['dosage'] ?? null;
            $quantity = (int)($med['qty'] ?? $med['quantity'] ?? 1);

            // Try to resolve medicine_id from name if not provided
            if (!$medicineId && $medicineName) {
                $stmt = $db->prepare("SELECT id FROM medicines WHERE name LIKE ? LIMIT 1");
                $stmt->execute(['%' . $medicineName . '%']);
                $medicineId = $stmt->fetchColumn() ?: null;
            }

            $stmt = $db->prepare("INSERT INTO prescription_medicines (prescription_id, medicine_id, medicine_name, dosage, quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $medicineId, $medicineName, $dosage, $quantity]);
            $linked[] = ['name' => $medicineName, 'medicine_id' => $medicineId, 'quantity' => $quantity];
        }

        return ResponseHandler::success(['linked' => $linked], count($linked) . ' medicine(s) linked to prescription.');
    }

    /**
     * POST /prescriptions/{id}/use
     * Record prescription usage for an order (reuse prevention)
     */
    public function recordUsage($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        $orderId = (int)($data['order_id'] ?? 0);

        if (!$orderId) return ResponseHandler::badRequest('Order ID required.');

        $prescription = $this->prescriptionModel->findById($id);
        if (!$prescription) return ResponseHandler::notFound('Prescription not found.');

        // Expiry check
        if ($this->isExpired($prescription)) {
            return ResponseHandler::badRequest('Prescription has expired and cannot be used.');
        }

        // Reuse prevention check
        $timesUsed = (int)($prescription['times_used'] ?? 0);
        $maxUses = (int)($prescription['max_uses'] ?? 1);

        if ($timesUsed >= $maxUses) {
            return ResponseHandler::badRequest("Prescription reuse limit reached ({$timesUsed}/{$maxUses}). Upload a new prescription.");
        }

        $db = \App\Core\Database::getInstance()->getConnection();

        // Check if already used for this order
        $stmt = $db->prepare("SELECT id FROM prescription_usage_log WHERE prescription_id = ? AND order_id = ?");
        $stmt->execute([$id, $orderId]);
        if ($stmt->fetch()) {
            return ResponseHandler::badRequest('Prescription already linked to this order.');
        }

        // Record usage
        $stmt = $db->prepare("INSERT INTO prescription_usage_log (prescription_id, order_id) VALUES (?, ?)");
        $stmt->execute([$id, $orderId]);

        // Increment usage counter
        $this->prescriptionModel->update($id, [
            'times_used' => $timesUsed + 1,
            'last_used_at' => date('Y-m-d H:i:s')
        ]);

        AuditLogger::log($user['id'], 'prescription_used', ['prescription_id' => $id, 'order_id' => $orderId]);

        return ResponseHandler::success([
            'uses_remaining' => $maxUses - ($timesUsed + 1)
        ], 'Prescription usage recorded.');
    }

    /**
     * GET /prescriptions/{id}/status
     * Check prescription validity (expiry + reuse status)
     */
    public function checkStatus($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        $prescription = $this->prescriptionModel->findById($id);
        if (!$prescription) return ResponseHandler::notFound('Prescription not found.');

        if ($prescription['user_id'] != $user['id'] && !in_array($user['role'], ['vendor', 'admin'])) {
            return ResponseHandler::forbidden();
        }

        $isExpired = $this->isExpired($prescription);
        $timesUsed = (int)($prescription['times_used'] ?? 0);
        $maxUses = (int)($prescription['max_uses'] ?? 1);
        $isReusable = $timesUsed < $maxUses;

        $daysRemaining = null;
        if (!empty($prescription['expiry_date'])) {
            $daysRemaining = (int)((strtotime($prescription['expiry_date']) - time()) / 86400);
        }

        return ResponseHandler::success([
            'id' => (int)$id,
            'status' => $prescription['status'],
            'is_expired' => $isExpired,
            'expiry_date' => $prescription['expiry_date'] ?? null,
            'days_remaining' => $daysRemaining,
            'times_used' => $timesUsed,
            'max_uses' => $maxUses,
            'is_reusable' => $isReusable,
            'can_use' => ($prescription['status'] === 'approved' && !$isExpired && $isReusable),
            'linked_medicines' => $this->prescriptionModel->getLinkedMedicines($id)
        ]);
    }

    /**
     * POST /prescriptions/{id}/add-to-cart
     * Add all linked medicines from an approved prescription to user's cart
     */
    public function addToCart($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();

        $prescription = $this->prescriptionModel->findById($id);
        if (!$prescription) return ResponseHandler::notFound('Prescription not found.');
        if ($prescription['user_id'] != $user['id']) return ResponseHandler::forbidden();

        // Validate prescription is usable
        if ($prescription['status'] !== 'approved') {
            return ResponseHandler::badRequest('Prescription must be approved before adding medicines to cart.');
        }
        if ($this->isExpired($prescription)) {
            return ResponseHandler::badRequest('Prescription has expired. Please upload a new one.');
        }

        $timesUsed = (int)($prescription['times_used'] ?? 0);
        $maxUses = (int)($prescription['max_uses'] ?? 1);
        if ($timesUsed >= $maxUses) {
            return ResponseHandler::badRequest('Prescription reuse limit reached. Upload a new prescription.');
        }

        $added = $this->autoLinkMedicinesToCart($id, $user['id']);

        return ResponseHandler::success([
            'added_medicines' => $added,
            'count' => count($added)
        ], count($added) . ' medicine(s) added to cart from prescription.');
    }

    // ─── PRIVATE HELPERS ─────────────────────────────────────────────

    /**
     * Check if a prescription is expired
     */
    private function isExpired(array $prescription): bool {
        // Check explicit expiry_date
        if (!empty($prescription['expiry_date'])) {
            return strtotime($prescription['expiry_date']) < time();
        }

        // Fallback: 180 days from issued_date or created_at
        $baseDate = $prescription['issued_date'] ?? $prescription['created_at'] ?? null;
        if ($baseDate) {
            $issuedAt = strtotime($baseDate);
            $ageInDays = (time() - $issuedAt) / 86400;
            return $ageInDays > 180;
        }

        return false;
    }

    /**
     * Auto-link prescription medicines to user's cart
     */
    private function autoLinkMedicinesToCart(int $prescriptionId, int $userId): array {
        $db = \App\Core\Database::getInstance()->getConnection();
        $cartModel = new Cart();
        $added = [];

        // Get linked medicines for this prescription
        $stmt = $db->prepare(
            "SELECT pm.*, m.id as resolved_medicine_id, m.name as resolved_name, m.price 
             FROM prescription_medicines pm
             LEFT JOIN medicines m ON m.id = pm.medicine_id
             WHERE pm.prescription_id = ? AND pm.added_to_cart = 0"
        );
        $stmt->execute([$prescriptionId]);
        $medicines = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($medicines as $med) {
            $medicineId = $med['resolved_medicine_id'] ?? $med['medicine_id'];
            
            if (!$medicineId) {
                // Try fuzzy match by name
                $nameStmt = $db->prepare("SELECT id, name, price FROM medicines WHERE name LIKE ? AND status = 'active' LIMIT 1");
                $nameStmt->execute(['%' . $med['medicine_name'] . '%']);
                $found = $nameStmt->fetch(\PDO::FETCH_ASSOC);
                if ($found) {
                    $medicineId = $found['id'];
                    // Update the link with resolved ID
                    $db->prepare("UPDATE prescription_medicines SET medicine_id = ? WHERE id = ?")->execute([$medicineId, $med['id']]);
                }
            }

            if ($medicineId) {
                $qty = (int)($med['quantity'] ?? 1);
                $cartModel->add($userId, $medicineId, $qty);
                
                // Mark as added to cart
                $db->prepare("UPDATE prescription_medicines SET added_to_cart = 1 WHERE id = ?")->execute([$med['id']]);
                
                $added[] = [
                    'medicine_id' => $medicineId,
                    'name' => $med['resolved_name'] ?? $med['medicine_name'],
                    'quantity' => $qty,
                    'price' => $med['price'] ?? 0
                ];
            }
        }

        return $added;
    }
}
