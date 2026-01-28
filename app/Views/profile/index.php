<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <!-- Back Button -->
    <a href="<?= BASE_URL ?>categories/index" class="back-button">
        <i class="bi bi-arrow-right"></i> العودة للرئيسية
    </a>

    <!-- Profile Card -->
    <div class="card-custom text-center">
        <h2 class="mb-4">
            <i class="bi bi-person-circle"></i> الملف الشخصي
        </h2>


        <!-- Profile Image -->
        <?php if ($user['profile_image']): ?>
            <img 
                src="<?= BASE_URL ?>uploads/profiles/<?= htmlspecialchars($user['profile_image']) ?>" 
                alt="Profile" 
                class="profile-image mb-3"
            >
        <?php else: ?>
            <div class="profile-image mb-3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                <i class="bi bi-person"></i>
            </div>
        <?php endif; ?>

        <!-- User Info -->
        <h3 class="mb-2"><?= htmlspecialchars($user['name']) ?></h3>
        <p class="text-muted mb-4">
            <i class="bi bi-envelope"></i> <?= htmlspecialchars($user['email']) ?>
        </p>

        <!-- Action Buttons -->
        <div class="d-flex flex-column gap-2">
            <a href="<?= BASE_URL ?>profile/edit" class="btn-custom btn-primary-custom">
                <i class="bi bi-pencil"></i> تعديل الملف الشخصي
            </a>
            <a href="<?= BASE_URL ?>auth/logout" class="btn-custom btn-danger-custom">
                <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
            </a>
        </div>
    </div>

    <!-- Account Info -->
    <div class="card-custom mt-3">
        <h4 class="mb-3">
            <i class="bi bi-info-circle"></i> معلومات الحساب
        </h4>
        <div class="d-flex justify-between mb-2">
            <span class="text-muted">تاريخ الإنشاء:</span>
            <strong><?= date('Y-m-d', strtotime($user['created_at'])) ?></strong>
        </div>
        <div class="d-flex justify-between">
            <span class="text-muted">آخر تحديث:</span>
            <strong><?= date('Y-m-d', strtotime($user['updated_at'])) ?></strong>
        </div>
    </div>

    <!-- Biometrics Section -->
    <div class="card-custom mt-3">
        <h4 class="mb-3">
            <i class="bi bi-shield-lock"></i> الأمان والبصمة
        </h4>
        
        <div class="list-custom">
            <!-- Face Enrollment -->
            <div class="list-item">
                <div class="d-flex justify-between align-center">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-person-bounding-box text-primary"></i> بصمة الوجه</h5>
                        <small class="text-muted"><?= $user['face_data'] ? 'مفعلة ✅' : 'غير مسجلة' ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="startFaceEnrollment()">
                        <?= $user['face_data'] ? 'تعديل' : 'تسجيل' ?>
                    </button>
                </div>
            </div>

            <!-- Fingerprint Enrollment -->
            <div class="list-item">
                <div class="d-flex justify-between align-center">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-fingerprint text-success"></i> بصمة الإصبع</h5>
                        <small class="text-muted"><?= $user['fingerprint_data'] ? 'مفعلة ✅' : 'غير مسجلة' ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-success" onclick="enrollFingerprint()">
                        <?= $user['fingerprint_data'] ? 'تعديل' : 'تسجيل' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Face Enrollment Modal -->
<div class="modal fade" id="faceEnrollModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-camera"></i> تسجيل بصمة الوجه</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="biometrics.stopCamera()"></button>
            </div>
            <div class="modal-body text-center">
                <div class="video-container mb-3" style="position: relative; overflow: hidden; border-radius: 20px; background: #1a1a1a; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: 4px solid var(--primary-color);">
                    <video id="faceEnrollVideo" width="100%" height="auto" autoplay playsinline style="transform: scaleX(-1);"></video>
                    
                    <!-- Overlay States -->
                    <div id="enrollOverlay" style="position: absolute; top:0; left:0; width:100%; height:100%; display:none; flex-direction:column; align-items:center; justify-content:center; background:rgba(0,0,0,0.7); color:white; backdrop-filter: blur(4px);">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <span id="overlayText" style="font-weight: 500;">جاري معالجة الوجه...</span>
                    </div>

                    <div id="enrollSuccess" style="position: absolute; top:0; left:0; width:100%; height:100%; display:none; flex-direction:column; align-items:center; justify-content:center; background:rgba(16, 185, 129, 0.9); color:white;">
                        <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
                        <h4 class="mt-3">تم التسجيل بنجاح!</h4>
                    </div>

                    <div id="enrollFailure" style="position: absolute; top:0; left:0; width:100%; height:100%; display:none; flex-direction:column; align-items:center; justify-content:center; background:rgba(239, 68, 68, 0.9); color:white;">
                        <i class="bi bi-x-circle-fill" style="font-size: 5rem;"></i>
                        <h4 class="mt-3">فشل التسجيل</h4>
                        <p id="failureMessage" class="px-3 text-center"></p>
                    </div>
                </div>
                <div class="alert alert-info py-2" style="font-size: 0.9rem; border-radius: 12px; border: none; background: #eff6ff; color: #1e40af;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <span id="enrollStatus">يرجى تثبيت الكاميرا والوميض بعينيك عند الضغط على "بدء"</span>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="biometrics.stopCamera()" style="border-radius: 12px;">إلغاء</button>
                <button type="button" class="btn btn-primary" id="btnStartCapture" onclick="captureAndEnroll()" style="border-radius: 12px; padding: 0.6rem 2rem; font-weight: 600;">بـدء الالتقاط</button>
            </div>
        </div>
    </div>
</div>

<script>
let faceEnrollModal = null;
let btnStartCapture = null;

document.addEventListener('DOMContentLoaded', function() {
    btnStartCapture = document.getElementById('btnStartCapture');
});

async function startFaceEnrollment() {
    const modalEl = document.getElementById('faceEnrollModal');
    if (!faceEnrollModal) {
        faceEnrollModal = new bootstrap.Modal(modalEl);
    }
    
    const video = document.getElementById('faceEnrollVideo');
    const started = await biometrics.startCamera(video);
    if (started) {
        faceEnrollModal.show();
    } else {
        alert('فشل الوصول للكاميرا. يرجى التحقق من الصلاحيات.');
    }
}

async function captureAndEnroll() {
    const video = document.getElementById('faceEnrollVideo');
    const overlay = document.getElementById('enrollOverlay');
    const overlayText = document.getElementById('overlayText');
    const successOverlay = document.getElementById('enrollSuccess');
    const failureOverlay = document.getElementById('enrollFailure');
    const failureMsg = document.getElementById('failureMessage');

    btnStartCapture.disabled = true;
    overlay.style.display = 'flex';
    overlayText.innerText = 'جاري التقاط ملامح الوجه...';

    // Reset states
    successOverlay.style.display = 'none';
    failureOverlay.style.display = 'none';

    try {
        // Capture 10 frames
        const frames = await biometrics.captureSequence(video, 10, 250);
        overlayText.innerText = 'جاري تحليل البيانات الحيوية...';

        const result = await biometrics.enrollFace(frames);
        
        if (result.status === 'success') {
            overlayText.innerText = 'تم التحليل! جاري حفظ البصمة...';
            
            const saveRes = await fetch('<?= BASE_URL ?>profile/updateBiometrics', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ face_data: result.embedding })
            });
            
            const saveResult = await saveRes.json();
            if (saveResult.status === 'success') {
                overlay.style.display = 'none';
                successOverlay.style.display = 'flex';
                setTimeout(() => location.reload(), 2000);
            } else {
                throw new Error(saveResult.message);
            }
        } else {
            throw new Error(result.detail || result.message || 'فشل التعرف على الوجه');
        }
    } catch (error) {
        console.error(error);
        overlay.style.display = 'none';
        failureOverlay.style.display = 'flex';
        failureMsg.innerText = error.message;
        btnStartCapture.disabled = false;
        btnStartCapture.innerText = 'إعادة المحاولة';
    } finally {
        biometrics.stopCamera();
    }
}

async function enrollFingerprint() {
    const result = await biometrics.registerFingerprint('<?= $user['email'] ?>');
    if (result.status === 'success') {
        const saveRes = await fetch('<?= BASE_URL ?>profile/updateBiometrics', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fingerprint_data: result.id })
        });
        
        const saveResult = await saveRes.json();
        if (saveResult.status === 'success') {
            alert('تم تفعيل بصمة الإصبع بنجاح');
            location.reload();
        } else {
            alert('فشل في حفظ البيانات: ' + saveResult.message);
        }
    } else {
        alert(result.message);
    }
}
</script>

<style>
.gap-2 {
    gap: 0.5rem;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
