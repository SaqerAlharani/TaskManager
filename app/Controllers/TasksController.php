<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\MainTask;
use App\Models\Subtask;
use App\Models\Category;
use App\Helpers\Session;
use App\Helpers\Validator;

/**
 * Task Controller
 * كونترولر المهام
 */
class TasksController extends Controller {
    
    private $mainTaskModel;
    private $subtaskModel;
    private $categoryModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->mainTaskModel = $this->model('MainTask');
        $this->subtaskModel = $this->model('Subtask');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * List all main tasks in a category
     */
    public function index($categoryId) {
        $userId = Session::getUserId();
        
        // Check category ownership
        if (!$this->categoryModel->belongsToUser($categoryId, $userId)) {
            Session::flash('error', 'غير مصرح لك بالوصول لهذا التصنيف');
            $this->redirect('categories/index');
            return;
        }
        
        $category = $this->categoryModel->findById($categoryId);
        $tasks = $this->mainTaskModel->getAllByCategory($categoryId);
        
        $this->view('tasks/index', [
            'title' => 'المهام - ' . $category['name'],
            'category' => $category,
            'tasks' => $tasks,
            'user' => Session::getUser(),
            'currentPage' => 'categories'
        ]);
    }
    
    /**
     * Create new main task
     */
    public function create($categoryId) {
        $userId = Session::getUserId();
        
        // Check category ownership
        if (!$this->categoryModel->belongsToUser($categoryId, $userId)) {
            Session::flash('error', 'غير مصرح لك بإضافة مهام لهذا التصنيف');
            $this->redirect('categories/index');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            // Validate
            $validator = new Validator();
            $validator->required('title', $title, 'عنوان المهمة مطلوب');
            $validator->minLength('title', $title, 3, 'عنوان المهمة يجب أن يكون 3 أحرف على الأقل');
            
            if ($validator->fails()) {
                Session::flash('error', implode('<br>', $validator->getErrors()));
                $this->redirect('tasks/index/' . $categoryId);
                return;
            }
            
            // Create task
            $taskData = [
                'title' => Validator::sanitize($title),
                'description' => Validator::sanitize($description),
                'status' => 'pending'
            ];
            
            if ($this->mainTaskModel->create($categoryId, $taskData)) {
                Session::flash('success', 'تم إضافة المهمة بنجاح');
            } else {
                Session::flash('error', 'حدث خطأ أثناء إضافة المهمة');
            }
            
            $this->redirect('tasks/index/' . $categoryId);
        }
    }
    
    /**
     * Edit main task
     */
    public function edit($taskId) {
        $userId = Session::getUserId();
        $task = $this->mainTaskModel->findById($taskId);
        
        if (!$task || $task['user_id'] != $userId) {
            Session::flash('error', 'غير مصرح لك بتعديل هذه المهمة');
            $this->redirect('categories/index');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'pending';
            
            // Validate
            $validator = new Validator();
            $validator->required('title', $title, 'عنوان المهمة مطلوب');
            $validator->minLength('title', $title, 3, 'عنوان المهمة يجب أن يكون 3 أحرف على الأقل');
            
            if ($validator->fails()) {
                Session::flash('error', implode('<br>', $validator->getErrors()));
                $this->redirect('tasks/index/' . $task['category_id']);
                return;
            }
            
            // Update task
            $taskData = [
                'title' => Validator::sanitize($title),
                'description' => Validator::sanitize($description),
                'status' => $status
            ];
            
            if ($this->mainTaskModel->update($taskId, $taskData)) {
                Session::flash('success', 'تم تحديث المهمة بنجاح');
            } else {
                Session::flash('error', 'حدث خطأ أثناء تحديث المهمة');
            }
            
            $this->redirect('tasks/index/' . $task['category_id']);
        }
    }
    
    /**
     * Delete main task
     */
    public function delete($taskId) {
        $userId = Session::getUserId();
        $task = $this->mainTaskModel->findById($taskId);
        
        if (!$task || $task['user_id'] != $userId) {
            Session::flash('error', 'غير مصرح لك بحذف هذه المهمة');
            $this->redirect('categories/index');
            return;
        }
        
        $categoryId = $task['category_id'];
        
        if ($this->mainTaskModel->deleteTask($taskId)) {
            Session::flash('success', 'تم حذف المهمة بنجاح');
        } else {
            Session::flash('error', 'حدث خطأ أثناء حذف المهمة');
        }
        
        $this->redirect('tasks/index/' . $categoryId);
    }
    
    /**
     * Toggle task status
     */
    public function toggleStatus($taskId) {
        $userId = Session::getUserId();
        $task = $this->mainTaskModel->findById($taskId);
        
        if (!$task || $task['user_id'] != $userId) {
            Session::flash('error', 'غير مصرح لك بتعديل هذه المهمة');
            $this->redirect('categories/index');
            return;
        }
        
        $this->mainTaskModel->toggleStatus($taskId);
        $this->redirect('tasks/index/' . $task['category_id']);
    }
    
    /**
     * List all subtasks for a main task
     */
    public function subtasks($mainTaskId) {
        $userId = Session::getUserId();
        $mainTask = $this->mainTaskModel->findById($mainTaskId);
        
        if (!$mainTask || $mainTask['user_id'] != $userId) {
            Session::flash('error', 'غير مصرح لك بالوصول لهذه المهمة');
            $this->redirect('categories/index');
            return;
        }
        
        $subtasks = $this->subtaskModel->getAllByMainTask($mainTaskId);
        
        $this->view('tasks/subtasks', [
            'title' => 'المهام الفرعية - ' . $mainTask['title'],
            'mainTask' => $mainTask,
            'subtasks' => $subtasks,
            'user' => Session::getUser()
        ]);
    }
    
    /**
     * Create new subtask
     */
    public function createSubtask($mainTaskId) {
        $userId = Session::getUserId();
        $mainTask = $this->mainTaskModel->findById($mainTaskId);
        
        if (!$mainTask || $mainTask['user_id'] != $userId) {
            Session::flash('error', 'غير مصرح لك بإضافة مهام فرعية لهذه المهمة');
            $this->redirect('categories/index');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            
            // Validate
            $validator = new Validator();
            $validator->required('title', $title, 'عنوان المهمة الفرعية مطلوب');
            $validator->minLength('title', $title, 2, 'عنوان المهمة الفرعية يجب أن يكون حرفين على الأقل');
            
            if ($validator->fails()) {
                Session::flash('error', implode('<br>', $validator->getErrors()));
                $this->redirect('tasks/subtasks/' . $mainTaskId);
                return;
            }
            
            // Create subtask
            if ($this->subtaskModel->create($mainTaskId, Validator::sanitize($title))) {
                Session::flash('success', 'تم إضافة المهمة الفرعية بنجاح');
            } else {
                Session::flash('error', 'حدث خطأ أثناء إضافة المهمة الفرعية');
            }
            
            $this->redirect('tasks/subtasks/' . $mainTaskId);
        }
    }
    
    /**
     * Toggle subtask status
     */
    public function toggleSubtaskStatus($subtaskId) {
        $userId = Session::getUserId();
        $subtask = $this->subtaskModel->findById($subtaskId);
        
        if (!$subtask || $subtask['user_id'] != $userId) {
            Session::flash('error', 'غير مصرح لك بتعديل هذه المهمة');
            $this->redirect('categories/index');
            return;
        }
        
        $this->subtaskModel->toggleStatus($subtaskId);
        $this->redirect('tasks/subtasks/' . $subtask['main_task_id']);
    }
    
    /**
     * Delete subtask
     */
    public function deleteSubtask($subtaskId) {
        $userId = Session::getUserId();
        $subtask = $this->subtaskModel->findById($subtaskId);
        
        if (!$subtask || $subtask['user_id'] != $userId) {
            Session::flash('error', 'غير مصرح لك بحذف هذه المهمة');
            $this->redirect('categories/index');
            return;
        }
        
        $mainTaskId = $subtask['main_task_id'];
        
        if ($this->subtaskModel->deleteSubtask($subtaskId)) {
            Session::flash('success', 'تم حذف المهمة الفرعية بنجاح');
        } else {
            Session::flash('error', 'حدث خطأ أثناء حذف المهمة الفرعية');
        }
        
        $this->redirect('tasks/subtasks/' . $mainTaskId);
    }
}
