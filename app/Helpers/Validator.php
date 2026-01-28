<?php
namespace App\Helpers;

/**
 * Validator Class
 * كلاس التحقق من المدخلات
 */
class Validator {
    
    private $errors = [];
    
    /**
     * Check if value is required (not empty)
     */
    public function required($field, $value, $message = null) {
        if (empty(trim($value))) {
            $this->errors[$field] = $message ?? "$field is required";
            return false;
        }
        return true;
    }
    
    /**
     * Validate email format
     */
    public function email($field, $value, $message = null) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Invalid email format";
            return false;
        }
        return true;
    }
    
    /**
     * Check minimum length
     */
    public function minLength($field, $value, $min, $message = null) {
        if (strlen($value) < $min) {
            $this->errors[$field] = $message ?? "$field must be at least $min characters";
            return false;
        }
        return true;
    }
    

    public function alphaNumeric($field, $value, $message = null)
        {
            if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                $this->errors[$field] = $message ?? "$field must contain letters and numbers only";
                return false;
            }
            return true;
        }

    /**
     * Check maximum length
     */
    public function maxLength($field, $value, $max, $message = null) {
        if (strlen($value) > $max) {
            $this->errors[$field] = $message ?? "$field must not exceed $max characters";
            return false;
        }
        return true;
    }
    
    /**
     * Check if two values match
     */
    public function match($field, $value1, $value2, $message = null) {
        if ($value1 !== $value2) {
            $this->errors[$field] = $message ?? "Values do not match";
            return false;
        }
        return true;
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Check if validation passed
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * Check if validation failed
     */
    public function fails() {
        return !$this->passes();
    }
    
    /**
     * Add manual error
     */
    public function addError($field, $message) {
        $this->errors[$field] = $message;
    }

    /**
     * Get first error for a field
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }
}
