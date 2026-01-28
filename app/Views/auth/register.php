<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <!-- App Header -->
    <div class="app-header app-header-register">
        <div class="app-logo">
            <i class="bi bi-person-plus-fill"></i> حساب جديد
        </div>
        <div class="app-subtitle">أهلاً بك! قم بإنشاء حساب للبدء</div>
    </div>

    <!-- Register Card -->
    <div class="card-custom">
        <h3 class="text-center mb-4">إنشاء حساب مستخدم جديد</h3>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert-custom alert-danger">
                <i class="bi bi-exclamation-circle"></i>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>auth/register" enctype="multipart/form-data" id="registerForm">
            <!-- Full Name -->
            <div class="form-group">
                <label class="form-label" for="name">
                    <i class="bi bi-person"></i> الاسم الكامل
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control-custom" 
                    placeholder="أدخل اسمك الكامل"
                    required
                    value="<?= $_POST['name'] ?? '' ?>"
                >
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">
                    <i class="bi bi-envelope"></i> البريد الإلكتروني
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control-custom" 
                    placeholder="example@email.com"
                    required
                    value="<?= $_POST['email'] ?? '' ?>"
                >
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="bi bi-lock"></i> كلمة المرور
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control-custom" 
                    placeholder="••••••••"
                    required
                    minlength="6"
                >
                <small class="text-muted text-small">يجب أن تكون 6 أحرف على الأقل</small>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label" for="confirm_password">
                    <i class="bi bi-lock-fill"></i> تأكيد كلمة المرور
                </label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="form-control-custom" 
                    placeholder="••••••••"
                    required
                >
            </div>

            <!-- Profile Image -->
            <div class="form-group">
                <label class="form-label" for="profile_image">
                    <i class="bi bi-image"></i> الصورة الشخصية (اختياري)
                </label>
                <input 
                    type="file" 
                    id="profile_image" 
                    name="profile_image" 
                    class="form-control-custom" 
                    accept="image/*"
                    onchange="previewImage(event)"
                >
                <img id="imagePreview" class="profile-image-preview" style="display: none;" alt="معاينة الصورة">
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn-custom btn-primary-custom">
                <i class="bi bi-person-check"></i> إنشاء الحساب
            </button>
        </form>

        <!-- Login Link -->
        <div class="text-center mt-3">
            <p class="text-muted text-small">
                لديك حساب بالفعل؟ 
                <a href="<?= BASE_URL ?>auth/login" class="link-custom">
                    تسجيل الدخول
                </a>
            </p>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
