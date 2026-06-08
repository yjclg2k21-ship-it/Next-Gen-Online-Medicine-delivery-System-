<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Models\KycDocument;
use App\Models\User;
use App\Models\AuditLog;
use Exception;

/**
 * KYC Controller — Aligned with Unified Schema
 */
class KycController extends BaseController {

    private $kycModel;
    private $userModel;
    private $auditModel;

    public function __construct() {
        $this->kycModel = new KycDocument();
        $this->userModel = new User();
        $this->auditModel = new AuditLog();
    }

    public function pending() {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            // Using a custom query for enrichment (can be moved to model if needed)
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT k.id, k.document_type as type, k.created_at as submittedOn, k.status, u.name, u.role as user_type 
                FROM kyc_documents k 
                JOIN users u ON k.user_id = u.id 
                WHERE k.status IN ('pending')
                ORDER BY k.created_at DESC
            ");
            $stmt->execute();
            $requests = $stmt->fetchAll();

            return ResponseHandler::success(['pending' => $requests]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/kyc/status/{userId}
     */
    public function status($id) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin' && $user['id'] != $id) {
            return ResponseHandler::forbidden();
        }

        $kycDocs = $this->kycModel->getByUser($id);
        $kyc = $kycDocs[0] ?? null;

        if (!$kyc) {
            return ResponseHandler::success([
                'status' => 'not_submitted',
                'required_docs' => ['aadhar', 'license', 'pharmacy_license', 'gst']
            ], 'KYC profile initialization required.');
        }

        return ResponseHandler::success($kyc, 'KYC status retrieved.');
    }

    /**
     * GET /api/v1/kyc/pending
     */
    public function getPending() {
        return $this->pending();
    }

    /**
     * POST /api/v1/kyc/submit
     */
    public function submit() {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        $data = $this->getPostData();
        
        $type = $data['document_type'] ?? 'aadhar'; // Defaulting to one of the enum values
        $docNumber = $data['document_number'] ?? '';

        // Handle File Upload
        $fileName = null;
        if (!empty($_FILES['document']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
            $fileName = 'kyc_' . $user['id'] . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../../storage/private/kyc/' . $fileName;
            
            if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
            move_uploaded_file($_FILES['document']['tmp_name'], $dest);
        }

        $id = $this->kycModel->create([
            'user_id' => $user['id'],
            'document_type' => $type,
            'document_number' => $docNumber,
            'document_image' => $fileName, // Changed from document_path to match schema
            'status' => 'pending'
        ]);

        $this->auditModel->log($user['id'], 'KYC_SUBMITTED', 'KycDocument', $id, ['type' => $type]);
        
        return ResponseHandler::success(['id' => $id], 'KYC document protocol injected.', 201);
    }

    /**
     * POST /api/v1/kyc/{id}/verify (Admin Only)
     */
    public function verify($id) {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        $this->kycModel->updateStatus($id, 'verified');

        // Logic parity: Update user status if KYC is verified
        $kyc = $this->kycModel->findById($id);
        if ($kyc) {
            $this->userModel->update($kyc['user_id'], ['status' => 'active', 'is_verified' => 1]);
        }

        $this->auditModel->log($admin['id'], 'KYC_VERIFIED', 'KycDocument', $id);
        
        return ResponseHandler::success([], 'KYC protocol verified. User status node transitioned to active.');
    }

    /**
     * POST /api/v1/kyc/{id}/reject (Admin Only)
     */
    public function reject($id) {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();
        
        $data = $this->getPostData();
        $reason = $data['reason'] ?? 'Invalid documentation artifact.';

        $this->kycModel->updateStatus($id, 'rejected', $reason);

        $this->auditModel->log($admin['id'], 'KYC_REJECTED', 'KycDocument', $id, ['reason' => $reason]);
        
        return ResponseHandler::success([], 'KYC protocol rejected.');
    }

    /**
     * GET /kyc/file/{filename}
     * Securely serve KYC document files (admin only)
     */
    public function serveFile($filename) {
        AuthGuard::handle();
        $user = AuthGuard::getUser();
        if ($user['role'] !== 'admin') return ResponseHandler::forbidden('Access denied.');

        // Sanitize filename - prevent directory traversal
        $filename = basename($filename);
        
        // Check both possible storage locations
        $paths = [
            __DIR__ . '/../../storage/private/kyc/' . $filename,
            __DIR__ . '/../../storage/uploads/kyc/' . $filename,
        ];

        $filePath = null;
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $filePath = $p;
                break;
            }
        }

        if (!$filePath) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'File not found', 'status' => 404]);
            exit;
        }

        // Serve the file
        $mime = mime_content_type($filePath) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=3600');
        readfile($filePath);
        exit;
    }
}

