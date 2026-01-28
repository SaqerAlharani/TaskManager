<?php
namespace App\Models;

use App\Core\Model;

/**
 * User Model
 * موديل المستخدمين
 */
class User extends Model {
    
    protected $table = 'users';
    
    /**
     * Create new user
     */
    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, profile_image) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['profile_image'] ?? null
        ]);
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        return $this->find($id);
    }
    
    /**
     * Update user profile
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $values[] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
        }
        
        if (isset($data['profile_image'])) {
            $fields[] = "profile_image = ?";
            $values[] = $data['profile_image'];
        }
        
        if (isset($data['face_data'])) {
            $fields[] = "face_data = ?";
            $values[] = $data['face_data'];
        }
        
        if (isset($data['fingerprint_data'])) {
            $fields[] = "fingerprint_data = ?";
            $values[] = $data['fingerprint_data'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
