<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\FileUpload;

/**
 * Authentication Controller
 * كونترولر المصادقة
 */
class AuthController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    /**
     * Login page and handler
     */
    public function login() {
        // If already logged in, redirect to categories
        if (Session::isLoggedIn()) {
            $this->redirect('categories/index');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Validate inputs
            $validator = new Validator();
            $validator->required('email', $email, 'البريد الإلكتروني مطلوب');
            $validator->email('email', $email, 'صيغة البريد الإلكتروني غير صحيحة');
            $validator->required('password', $password, 'كلمة المرور مطلوبة');
            
            if ($validator->fails()) {
                $this->view('auth/login', [
                    'title' => 'تسجيل الدخول',
                    'error' => implode('<br>', $validator->getErrors())
                ]);
                return;
            }
            
            // Find user by email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                $this->view('auth/login', [
                    'title' => 'تسجيل الدخول',
                    'error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
                ]);
                return;
            }
            
            // Verify password
            if (!$this->userModel->verifyPassword($password, $user['password'])) {
                $this->view('auth/login', [
                    'title' => 'تسجيل الدخول',
                    'error' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
                ]);
                return;
            }
            
            // Set session
            Session::set('user_id', $user['id']);
            Session::set('user', [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'profile_image' => $user['profile_image']
            ]);
            
            // Redirect to categories
            $this->redirect('categories/index');
        } else {
            // Show login form
            $this->view('auth/login', [
                'title' => 'تسجيل الدخول'
            ]);
        }
    }
    
    /**
     * Register page and handler
     */
    public function register() {
        // If already logged in, redirect to categories
        if (Session::isLoggedIn()) {
            $this->redirect('categories/index');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate inputs
            $validator = new Validator();
            $validator->required('name', $name, 'الاسم مطلوب');
            $validator->minLength('name', $name, 3, 'الاسم يجب أن يكون 3 أحرف على الأقل');
            $validator->alphaNumeric('name', $name,   'الاسم غير صالح');
            $validator->required('email', $email, 'البريد الإلكتروني مطلوب');
            $validator->email('email', $email, 'صيغة البريد الإلكتروني غير صحيحة');
            $validator->required('password', $password, 'كلمة المرور مطلوبة');
            $validator->minLength('password', $password, 6, 'كلمة المرور يجب أن تكون 6 أحرف على الأقل');
            $validator->match('confirm_password', $password, $confirmPassword, 'كلمات المرور غير متطابقة');
            
            // Check if email exists
            if ($this->userModel->emailExists($email)) {
                $validator->addError('email', 'البريد الإلكتروني مستخدم بالفعل');
            }
            
            if ($validator->fails()) {
                $this->view('auth/register', [
                    'title' => 'إنشاء حساب',
                    'errors' => $validator->getErrors()
                ]);
                return;
            }
            
            // Handle profile image upload
            $profileImage = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $fileUpload = new FileUpload();
                $uploadResult = $fileUpload->upload($_FILES['profile_image']);
                
                if ($uploadResult['success']) {
                    $profileImage = $uploadResult['filename'];
                }
            }
            
            // Create user
            $userData = [
                'name' => Validator::sanitize($name),
                'email' => Validator::sanitize($email),
                'password' => $password,
                'profile_image' => $profileImage
            ];
            
            if ($this->userModel->create($userData)) {
                // Get the created user
                $user = $this->userModel->findByEmail($email);
                
                if ($user) {
                    // Set session
                    Session::set('user_id', $user['id']);
                    Session::set('user', [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'profile_image' => $user['profile_image']
                    ]);
                    
                    Session::flash('success', 'تم إنشاء الحساب بنجاح!');
                    $this->redirect('categories/index');
                } else {
                    $this->redirect('auth/login');
                }
            } else {
                $this->view('auth/register', [
                    'title' => 'إنشاء حساب',
                    'errors' => ['حدث خطأ أثناء إنشاء الحساب']
                ]);
            }
        } else {
            // Show register form
            $this->view('auth/register', [
                'title' => 'إنشاء حساب'
            ]);
        }
    }
    
    /**
     * Biometric Login Handler (AJAX)
     */
    public function biometricLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $email = trim($data['email'] ?? '');
            $type = $data['type'] ?? ''; // 'face' or 'fingerprint'

            if (!$email) {
                echo json_encode(['status' => 'error', 'message' => 'البريد الإلكتروني مطلوب']);
                return;
            }

            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'الحساب غير موجود']);
                return;
            }

            if ($type === 'face') {
                $images = $data['images'] ?? [];
                $faceData = $user['face_data'];

                if (!$faceData) {
                    echo json_encode(['status' => 'error', 'message' => 'لم يتم إعداد بصمة الوجه لهذا الحساب']);
                    return;
                }

                // Call Python API for verification
                $apiUrl = "https://loving-light-production-c248.up.railway.app/verify";
                $postData = json_encode([
                    'images' => $images,
                    'reference_embedding' => $faceData
                ]);

                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local/production flexibility

                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    echo json_encode(['status' => 'error', 'message' => 'تعذر الاتصال بخادم التحقق: ' . $error]);
                    return;
                }

                $resData = json_decode($response, true);
                if ($resData['status'] === 'approved') {
                    $this->loginUser($user);
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => $resData['message'] ?? 'فشل التحقق من الوجه']);
                }
            } 
            else if ($type === 'fingerprint') {
                $fingerprintId = $data['id'] ?? '';
                if ($user['fingerprint_data'] === $fingerprintId) {
                    $this->loginUser($user);
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'بصمة الإصبع غير متطابقة']);
                }
            }
        }
    }

    /**
     * Helper to log in a user and set sessions
     */
    private function loginUser($user) {
        Session::set('user_id', $user['id']);
        Session::set('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'profile_image' => $user['profile_image']
        ]);
    }

    /**
     * Logout
     */
    public function logout() {
        Session::destroy();
        $this->redirect('auth/login');
    }
}
