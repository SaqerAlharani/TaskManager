<?php
namespace App\Models;

use App\Core\Model;

/**
 * Subtask Model
 * موديل المهام الفرعية
 */
class Subtask extends Model {
    
    protected $table = 'subtasks';
    
    /**
     * Create new subtask
     */
    public function create($mainTaskId, $title) {
        $sql = "INSERT INTO subtasks (main_task_id, title, status) VALUES (?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$mainTaskId, $title]);
    }
    
    /**
     * Get all subtasks by main task (JOIN query)
     */
    public function getAllByMainTask($mainTaskId) {
        $sql = "
            SELECT 
                st.*,
                mt.title as main_task_title,
                mt.category_id,
                c.user_id
            FROM subtasks st
            INNER JOIN main_tasks mt ON st.main_task_id = mt.id
            INNER JOIN categories c ON mt.category_id = c.id
            WHERE st.main_task_id = ?
            ORDER BY st.created_at ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mainTaskId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Find subtask by ID with parent info (JOIN query)
     */
    public function findById($id) {
        $sql = "
            SELECT 
                st.*,
                mt.title as main_task_title,
                mt.category_id,
                c.user_id
            FROM subtasks st
            INNER JOIN main_tasks mt ON st.main_task_id = mt.id
            INNER JOIN categories c ON mt.category_id = c.id
            WHERE st.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Update subtask
     */
    public function update($id, $title) {
        $sql = "UPDATE subtasks SET title = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $id]);
    }
    
    /**
     * Toggle subtask status
     */
    public function toggleStatus($id) {
        $sql = "UPDATE subtasks SET status = IF(status = 'pending', 'completed', 'pending') WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Delete subtask
     */
    public function deleteSubtask($id) {
        return $this->delete($id);
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
