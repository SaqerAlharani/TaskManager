<?php
namespace App\Helpers;

/**
 * File Upload Helper Class
 * كلاس مساعد لرفع الملفات
 */
class FileUpload {
    
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxSize = 5242880; // 5MB in bytes
    private $uploadDir;
    
    public function __construct($uploadDir = 'uploads/profiles/') {
        $this->uploadDir = __DIR__ . '/../../public/' . $uploadDir;
        
        // Create directory if not exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload file
     */
    public function upload($file) {
        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or upload error'];
        }
        
        // Validate file type
        if (!$this->validateType($file)) {
            return ['success' => false, 'message' => 'Invalid file type. Only images allowed'];
        }
        
        // Validate file size
        if (!$this->validateSize($file)) {
            return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
        }
        
        // Generate unique filename
        $filename = $this->generateUniqueFileName($file);
        $destination = $this->uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
    
    /**
     * Validate file type
     */
    private function validateType($file) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($extension, $this->allowedExtensions);
    }
    
    /**
     * Validate file size
     */
    private function validateSize($file) {
        return $file['size'] <= $this->maxSize;
    }
    
    /**
     * Generate unique filename
     */
    private function generateUniqueFileName($file) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return uniqid('profile_', true) . '.' . $extension;
    }
    
    /**
     * Delete file
     */
    public function delete($filename) {
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}
