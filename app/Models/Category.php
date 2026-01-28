<?php
namespace App\Models;

use App\Core\Model;

/**
 * Category Model
 * موديل التصنيفات
 */
class Category extends Model {
    
    protected $table = 'categories';
    
    /**
     * Create new category
     */
    public function create($userId, $name) {
        $sql = "INSERT INTO categories (user_id, name) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $name]);
    }
    
    /**
     * Get all categories for user with task count (JOIN query)
     */
    public function getAllByUser($userId) {
        $sql = "
            SELECT 
                c.*,
                COUNT(mt.id) as task_count
            FROM categories c
            LEFT JOIN main_tasks mt ON c.id = mt.category_id
            WHERE c.user_id = ?
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Find category by ID
     */
    public function findById($id) {
        return $this->find($id);
    }
    
    /**
     * Update category
     */
    public function update($id, $name) {
        $sql = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $id]);
    }
    
    /**
     * Delete category
     */
    public function deleteCategory($id) {
        return $this->delete($id);
    }
    
    /**
     * Check if category belongs to user
     */
    public function belongsToUser($id, $userId) {
        $stmt = $this->db->prepare("SELECT id FROM categories WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
