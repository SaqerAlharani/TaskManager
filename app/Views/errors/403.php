<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <div class="empty-state" style="padding: 4rem 1rem;">
        <div class="empty-state-icon" style="font-size: 6rem; color: #ef4444;">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">403</h1>
        <h3>الوصول مرفوض</h3>
        <p class="text-muted mb-4">يجب عليك تسجيل الدخول للوصول إلى هذه الصفحة</p>
        
        <a href="<?= BASE_URL ?>auth/login" class="btn-custom btn-primary-custom" style="width: auto; padding: 0.75rem 2rem;">
            <i class="bi bi-box-arrow-in-right"></i> تسجيل الدخول
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
