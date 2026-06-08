<?php
namespace App\Services;

/**
 * File Storage Service
 * Handles uploading local files or dumping to AWS S3/Cloud Storage.
 */
class FileStorageService {

    public static function store($file, $directory = 'uploads') {
        $uploadDir = __DIR__ . '/../../public/' . $directory . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/public/' . $directory . '/' . $filename;
        }

        return false;
    }
}
