<?php
namespace App\Models;

use App\Core\Model;

/**
 * Main Task Model
 * موديل المهام الرئيسية
 */
class MainTask extends Model {
    
    protected $table = 'main_tasks';
    
    /**
     * Create new main task
     */
    public function create($categoryId, $data) {
        $sql = "INSERT INTO main_tasks (category_id, title, description, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $categoryId,
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? 'pending'
        ]);
    }
    
    /**
     * Get all main tasks by category with subtask count (JOIN query)
     */
    public function getAllByCategory($categoryId) {
        $sql = "
            SELECT 
                mt.*,
                c.name as category_name,
                c.user_id,
                COUNT(st.id) as subtask_count,
                SUM(CASE WHEN st.status = 'completed' THEN 1 ELSE 0 END) as completed_subtasks
            FROM main_tasks mt
            INNER JOIN categories c ON mt.category_id = c.id
            LEFT JOIN subtasks st ON mt.id = st.main_task_id
            WHERE mt.category_id = ?
            GROUP BY mt.id
            ORDER BY mt.created_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Find task by ID with category info (JOIN query)
     */
    public function findById($id) {
        $sql = "
            SELECT 
                mt.*,
                c.name as category_name,
                c.user_id
            FROM main_tasks mt
            INNER JOIN categories c ON mt.category_id = c.id
            WHERE mt.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Update main task
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        if (isset($data['title'])) {
            $fields[] = "title = ?";
            $values[] = $data['title'];
        }
        
        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $values[] = $data['description'];
        }
        
        if (isset($data['status'])) {
            $fields[] = "status = ?";
            $values[] = $data['status'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE main_tasks SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Toggle task status
     */
    public function toggleStatus($id) {
        $sql = "UPDATE main_tasks SET status = IF(status = 'pending', 'completed', 'pending') WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Delete main task
     */
    public function deleteTask($id) {
        return $this->delete($id);
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
