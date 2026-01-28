<?php
namespace App\Helpers;

/**
 * Session Helper Class
 * كلاس مساعد للجلسات
 */
class Session {
    
    /**
     * Start session if not started
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set session variable
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session variable
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session variable exists
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session variable
     */
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Destroy session
     */
    public static function destroy() {
        self::start();
        session_unset();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
                    );
                }
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return self::has('user_id');
    }
    
    /**
     * Get current user ID
     */
    public static function getUserId() {
        return self::get('user_id');
    }
    
    /**
     * Get current user data
     */
    public static function getUser() {
        return self::get('user');
    }
    
    /**
     * Set flash message
     */
    public static function flash($key, $message) {
        self::set('flash_' . $key, $message);
    }
    
    /**
     * Get and remove flash message
     */
    public static function getFlash($key) {
        $message = self::get('flash_' . $key);
        self::remove('flash_' . $key);
        return $message;
    }
}
