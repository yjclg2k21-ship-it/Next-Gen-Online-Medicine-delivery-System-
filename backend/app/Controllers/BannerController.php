<?php
namespace App\Controllers;

use App\Core\ResponseHandler;
use App\Middleware\AuthGuard;
use App\Models\Banner;
use Exception;

/**
 * Banner Controller — Admin CMS for public-facing clinical showcase.
 */
class BannerController extends BaseController {
    
    private $bannerModel;

    public function __construct() {
        $this->bannerModel = new Banner();
    }

    public function index() {
        try {
            // Publicly accessible for homepage showcase
            $banners = $this->bannerModel->findAll();
            return ResponseHandler::success(['banners' => $banners]);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function create() {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        $data = $this->getPostData();
        if (empty($data['title']) || empty($data['image_url'])) {
            return ResponseHandler::badRequest('Protocol error: Banner title and image_url required.');
        }

        try {
            $id = $this->bannerModel->create([
                'title' => $data['title'],
                'image_url' => $data['image_url'],
                'link' => $data['link'] ?? null,
                'status' => $data['status'] ?? 'active'
            ]);
            return ResponseHandler::success(['id' => $id], 'Banner artifact injected.', 201);
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function toggle($id) {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            $banner = $this->bannerModel->findById($id);
            if (!$banner) return ResponseHandler::notFound('Banner node missing.');

            $newStatus = ($banner['status'] === 'active') ? 'inactive' : 'active';
            $this->bannerModel->update($id, ['status' => $newStatus]);
            
            return ResponseHandler::success(['status' => $newStatus], "Banner #$id transitioned to $newStatus.");
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function delete($id) {
        AuthGuard::handle();
        $admin = AuthGuard::getUser();
        if ($admin['role'] !== 'admin') return ResponseHandler::forbidden();

        try {
            $this->bannerModel->delete($id);
            return ResponseHandler::success([], 'Banner node decoupled.');
        } catch (Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
