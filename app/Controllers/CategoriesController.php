<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Helpers\Session;
use App\Helpers\Validator;

/**
 * Category Controller
 * كونترولر التصنيفات
 */
class CategoriesController extends Controller {
    
    private $categoryModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * List all categories
     */
    public function index() {
        $userId = Session::getUserId();
        $categories = $this->categoryModel->getAllByUser($userId);
        
        $this->view('categories/index', [
            'title' => 'تصنيفاتي',
            'categories' => $categories,
            'user' => Session::getUser(),
            'currentPage' => 'categories'
        ]);
    }
    
    /**
     * Create new category
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $userId = Session::getUserId();
            
            // Validate
            $validator = new Validator();
            $validator->required('name', $name, 'اسم التصنيف مطلوب');
            $validator->minLength('name', $name, 2, 'اسم التصنيف يجب أن يكون حرفين على الأقل');
            
            if ($validator->fails()) {
                Session::flash('error', implode('<br>', $validator->getErrors()));
                $this->redirect('categories/index');
                return;
            }
            
            // Create category
            if ($this->categoryModel->create($userId, Validator::sanitize($name))) {
                Session::flash('success', 'تم إضافة التصنيف بنجاح');
            } else {
                Session::flash('error', 'حدث خطأ أثناء إضافة التصنيف');
            }
            
            $this->redirect('categories/index');
        }
    }
    
    /**
     * Edit category
     */
    public function edit($id) {
        $userId = Session::getUserId();
        
        // Check ownership
        if (!$this->categoryModel->belongsToUser($id, $userId)) {
            Session::flash('error', 'غير مصرح لك بتعديل هذا التصنيف');
            $this->redirect('categories/index');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            
            // Validate
            $validator = new Validator();
            $validator->required('name', $name, 'اسم التصنيف مطلوب');
            $validator->minLength('name', $name, 2, 'اسم التصنيف يجب أن يكون حرفين على الأقل');
            
            if ($validator->fails()) {
                Session::flash('error', implode('<br>', $validator->getErrors()));
                $this->redirect('categories/index');
                return;
            }
            
            // Update category
            if ($this->categoryModel->update($id, Validator::sanitize($name))) {
                Session::flash('success', 'تم تحديث التصنيف بنجاح');
            } else {
                Session::flash('error', 'حدث خطأ أثناء تحديث التصنيف');
            }
            
            $this->redirect('categories/index');
        }
    }
    
    /**
     * Delete category
     */
    public function delete($id) {
        $userId = Session::getUserId();
        
        // Check ownership
        if (!$this->categoryModel->belongsToUser($id, $userId)) {
            Session::flash('error', 'غير مصرح لك بحذف هذا التصنيف');
            $this->redirect('categories/index');
            return;
        }
        
        // Delete category
        if ($this->categoryModel->deleteCategory($id)) {
            Session::flash('success', 'تم حذف التصنيف بنجاح');
        } else {
            Session::flash('error', 'حدث خطأ أثناء حذف التصنيف');
        }
        
        $this->redirect('categories/index');
    }
}
