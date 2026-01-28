<?php
namespace App\Core;

 
class Controller {
    
    protected function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Check if view file exists
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $view);
        }
    }
    
    /**
     * Load model
     */
    protected function model($model) {
        $modelClass = "App\\Models\\" . $model;
        if (class_exists($modelClass)) {
            return new $modelClass();
        } else {
            die("Model not found: " . $model);
        }
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        session_write_close();
        header("Location: " . BASE_URL . $url);
        exit;
    }
    
    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!\App\Helpers\Session::isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }
}
