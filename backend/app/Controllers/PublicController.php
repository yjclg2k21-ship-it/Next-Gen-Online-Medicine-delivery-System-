<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\ResponseHandler;

class PublicController extends Controller {
    
    public function getBlogs() {
        try {
            $stmt = $this->db->query("SELECT id, title, slug, LEFT(content, 150) as excerpt, cover_image, author_name, created_at FROM blogs WHERE status = 'published' ORDER BY created_at DESC");
            $blogs = $stmt->fetchAll();
            return ResponseHandler::success(['blogs' => $blogs], 'Blogs retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }

    public function getBlogBySlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM blogs WHERE slug = ? AND status = 'published'");
            $stmt->execute([$slug]);
            $blog = $stmt->fetch();
            
            if (!$blog) {
                return ResponseHandler::error('Blog not found.', 404);
            }
            return ResponseHandler::success(['blog' => $blog], 'Blog retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
