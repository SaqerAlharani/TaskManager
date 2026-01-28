<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\FileUpload;

/**
 * Profile Controller
 * كونترولر الملف الشخصي
 */
class ProfileController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->userModel = $this->model('User');
    }
    
    /**
     * View profile
     */
    public function index() {
        $userId = Session::getUserId();
        $user = $this->userModel->findById($userId);
        
        $this->view('profile/index', [
            'title' => 'الملف الشخصي',
            'user' => $user,
            'currentPage' => 'profile'
        ]);
    }
    
    /**
     * Edit profile
     */
    public function edit() {
        $userId = Session::getUserId();
        $user = $this->userModel->findById($userId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            // Validate
            $validator = new Validator();
            $validator->required('name', $name, 'الاسم مطلوب');
            $validator->minLength('name', $name, 3, 'الاسم يجب أن يكون 3 أحرف على الأقل');
            $validator->required('email', $email, 'البريد الإلكتروني مطلوب');
            $validator->email('email', $email, 'صيغة البريد الإلكتروني غير صحيحة');
            
            // Check if email exists (excluding current user)
            if ($this->userModel->emailExists($email, $userId)) {
                $validator->getErrors()['email'] = 'البريد الإلكتروني مستخدم بالفعل';
            }
            
            if ($validator->fails()) {
                $this->view('profile/edit', [
                    'title' => 'تعديل الملف الشخصي',
                    'user' => $user,
                    'errors' => $validator->getErrors()
                ]);
                return;
            }
            
            // Prepare update data
            $updateData = [
                'name' => Validator::sanitize($name),
                'email' => Validator::sanitize($email)
            ];
            
            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $fileUpload = new FileUpload();
                $uploadResult = $fileUpload->upload($_FILES['profile_image']);
                
                if ($uploadResult['success']) {
                    // Delete old image if exists
                    if ($user['profile_image']) {
                        $fileUpload->delete($user['profile_image']);
                    }
                    
                    $updateData['profile_image'] = $uploadResult['filename'];
                }
            }
            
            // Update user
            if ($this->userModel->update($userId, $updateData)) {
                // Update session
                $updatedUser = $this->userModel->findById($userId);
                Session::set('user', [
                    'id' => $updatedUser['id'],
                    'name' => $updatedUser['name'],
                    'email' => $updatedUser['email'],
                    'profile_image' => $updatedUser['profile_image']
                ]);
                
                Session::flash('success', 'تم تحديث الملف الشخصي بنجاح');
                $this->redirect('profile/index');
            } else {
                $this->view('profile/edit', [
                    'title' => 'تعديل الملف الشخصي',
                    'user' => $user,
                    'errors' => ['حدث خطأ أثناء تحديث الملف الشخصي']
                ]);
            }
        } else {
            $this->view('profile/edit', [
                'title' => 'تعديل الملف الشخصي',
                'user' => $user
            ]);
        }
    }

    /**
     * Update Biometric Data (AJAX)
     */
    public function updateBiometrics() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Session::getUserId();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updateData = [];
            if (isset($data['face_data'])) {
                $updateData['face_data'] = $data['face_data'];
            }
            if (isset($data['fingerprint_data'])) {
                $updateData['fingerprint_data'] = $data['fingerprint_data'];
            }

            if (empty($updateData)) {
                echo json_encode(['status' => 'error', 'message' => 'No data provided']);
                return;
            }

            if ($this->userModel->update($userId, $updateData)) {
                echo json_encode(['status' => 'success', 'message' => 'تم تحديث البيانات البيومترية بنجاح']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء تحديث البيانات']);
            }
        }
    }
}
