<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <!-- Back Button -->
    <a href="<?= BASE_URL ?>profile/index" class="back-button">
        <i class="bi bi-arrow-right"></i> العودة للملف الشخصي
    </a>

    <!-- Edit Profile Card -->
    <div class="card-custom">
        <h2 class="text-center mb-4">
            <i class="bi bi-pencil-square"></i> تعديل الملف الشخصي
        </h2>

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

        <form method="POST" action="<?= BASE_URL ?>profile/edit" enctype="multipart/form-data">
            <!-- Current Profile Image -->
            <div class="text-center mb-3">
                <?php if ($user['profile_image']): ?>
                    <img 
                        id="currentImage"
                        src="<?= BASE_URL ?>uploads/profiles/<?= htmlspecialchars($user['profile_image']) ?>" 
                        alt="Profile" 
                        class="profile-image-preview"
                        style="display: block;"
                    >
                <?php else: ?>
                    <img 
                        id="currentImage"
                        class="profile-image-preview" 
                        style="display: none;"
                        alt="معاينة الصورة"
                    >
                    <div id="noImage" class="profile-image-preview" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <i class="bi bi-person"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">
                    <i class="bi bi-person"></i> الاسم الكامل
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control-custom" 
                    value="<?= htmlspecialchars($user['name']) ?>"
                    required
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
                    value="<?= htmlspecialchars($user['email']) ?>"
                    required
                >
            </div>

            <!-- Profile Image -->
            <div class="form-group">
                <label class="form-label" for="profile_image">
                    <i class="bi bi-image"></i> تغيير الصورة الشخصية
                </label>
                <input 
                    type="file" 
                    id="profile_image" 
                    name="profile_image" 
                    class="form-control-custom" 
                    accept="image/*"
                    onchange="previewNewImage(event)"
                >
                <small class="text-muted text-small">اترك الحقل فارغاً إذا كنت لا تريد تغيير الصورة</small>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-custom btn-primary-custom">
                <i class="bi bi-check-lg"></i> حفظ التغييرات
            </button>
        </form>
    </div>
</div>

<script>
function previewNewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('currentImage');
    const noImage = document.getElementById('noImage');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (noImage) {
                noImage.style.display = 'none';
            }
        }
        reader.readAsDataURL(file);
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
