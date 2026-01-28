<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <!-- App Header -->
    <div class="app-header">
        <div class="app-logo">
            <i class="bi bi-check2-circle"></i> Task Manager
        </div>
        <div class="app-subtitle">نظم مهامك بذكاء</div>
    </div>

    <!-- Login Card -->
    <div class="card-custom">
        <h2 class="text-center mb-4">تسجيل الدخول</h2>

        <?php if (isset($error)): ?>
            <div class="alert-custom alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert-custom alert-success">
                <i class="bi bi-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>auth/login">
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
                >
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn-custom btn-primary-custom">
                <i class="bi bi-box-arrow-in-right"></i> تسجيل الدخول
            </button>
        </form>

        <!-- Register Link -->
        <div class="text-center mt-3">
            <p class="text-muted text-small">
                ليس لديك حساب؟ 
                <a href="<?= BASE_URL ?>auth/register" class="link-custom">
                    إنشاء حساب جديد
                </a>
            </p>
        </div>

        <hr class="my-4">

        <!-- Biometric Login Options -->
        <div class="biometric-options">
            <p class="text-center text-muted text-small mb-3">أو قم بتسجيل الدخول السريع</p>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary w-100" onclick="startBiometricLogin('face')">
                    <i class="bi bi-person-bounding-box"></i> بصمة الوجه
                </button>
                <button type="button" class="btn btn-outline-success w-100" onclick="startBiometricLogin('fingerprint')">
                    <i class="bi bi-fingerprint"></i> بصمة الإصبع
                </button>
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="text-center mt-4 text-muted text-small">
        <p>© 2026 Task Manager. جميع الحقوق محفوظة.</p>
    </div>
</div>

<!-- Biometric Login Modal -->
<div class="modal fade" id="biometricLoginModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-shield-shaded"></i> التحقق البيومتري</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="biometrics.stopCamera()"></button>
            </div>
            <div class="modal-body text-center">
                <div id="faceLoginContainer" style="display: none;">
                    <div class="video-container mb-3" style="position: relative; overflow: hidden; border-radius: 20px; background: #1a1a1a; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: 4px solid var(--primary-color);">
                        <video id="faceLoginVideo" width="100%" height="auto" autoplay playsinline style="transform: scaleX(-1);"></video>
                        
                        <div id="loginOverlay" style="position: absolute; top:0; left:0; width:100%; height:100%; display:none; flex-direction:column; align-items:center; justify-content:center; background:rgba(0,0,0,0.7); color:white; backdrop-filter: blur(4px);">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                            <span id="loginOverlayText" style="font-weight: 500;">جاري التحقق من الوجه...</span>
                        </div>

                        <div id="loginSuccess" style="position: absolute; top:0; left:0; width:100%; height:100%; display:none; flex-direction:column; align-items:center; justify-content:center; background:rgba(16, 185, 129, 0.9); color:white;">
                            <i class="bi bi-shield-check" style="font-size: 5rem;"></i>
                            <h4 class="mt-3">تم التحقق! جاري الدخول...</h4>
                        </div>

                        <div id="loginFailure" style="position: absolute; top:0; left:0; width:100%; height:100%; display:none; flex-direction:column; align-items:center; justify-content:center; background:rgba(239, 68, 68, 0.9); color:white;">
                            <i class="bi bi-shield-x" style="font-size: 5rem;"></i>
                            <h4 class="mt-3">فشل التحقق</h4>
                            <p id="loginFailureMessage" class="px-3 text-center"></p>
                        </div>
                    </div>
                    <p class="mb-0 text-muted">يرجى تثبيت الكاميرا والوميض بعينيك</p>
                </div>
                <div id="fingerprintLoginContainer" style="display: none;" class="py-4">
                    <div class="fingerprint-scanner mb-3">
                        <i class="bi bi-fingerprint text-success" style="font-size: 5rem;"></i>
                    </div>
                    <p class="mb-0">يرجى لمس مستشعر البصمة في جهازك للتحقق</p>
                </div>
                <div id="loginStatus" class="alert mt-3" style="display: none; border-radius: 12px; border: none;"></div>
            </div>
            <div class="modal-footer border-0" id="faceLoginFooter" style="display: none;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="biometrics.stopCamera()" style="border-radius: 12px;">إلغاء</button>
                <button type="button" class="btn btn-primary" id="btnCaptureLogin" onclick="captureAndLogin()" style="border-radius: 12px; padding: 0.6rem 2rem; font-weight: 600;">بدء التحقق</button>
            </div>
        </div>
    </div>
</div>

<script>
const biometricModal = new bootstrap.Modal(document.getElementById('biometricLoginModal'));
const loginStatus = document.getElementById('loginStatus');

async function startBiometricLogin(type) {
    const email = document.getElementById('email').value.trim();
    if (!email) {
        alert('يرجى إدخال البريد الإلكتروني أولاً لتفعيل الدخول بالبصمة');
        document.getElementById('email').focus();
        return;
    }

    if (type === 'face') {
        document.getElementById('faceLoginContainer').style.display = 'block';
        document.getElementById('fingerprintLoginContainer').style.display = 'none';
        document.getElementById('faceLoginFooter').style.display = 'flex';
        
        const video = document.getElementById('faceLoginVideo');
        const started = await biometrics.startCamera(video);
        if (started) {
            biometricModal.show();
        } else {
            alert('فشل الوصول للكاميرا');
        }
    } else {
        document.getElementById('faceLoginContainer').style.display = 'none';
        document.getElementById('fingerprintLoginContainer').style.display = 'block';
        document.getElementById('faceLoginFooter').style.display = 'none';
        biometricModal.show();
        loginWithFingerprint(email);
    }
}

async function captureAndLogin() {
    const email = document.getElementById('email').value.trim();
    const video = document.getElementById('faceLoginVideo');
    const overlay = document.getElementById('loginOverlay');
    const overlayText = document.getElementById('loginOverlayText');
    const successOverlay = document.getElementById('loginSuccess');
    const failureOverlay = document.getElementById('loginFailure');
    const failureMsg = document.getElementById('loginFailureMessage');
    const btn = document.getElementById('btnCaptureLogin');

    btn.disabled = true;
    overlay.style.display = 'flex';
    overlayText.innerText = 'جاري تحليل ملامح الوجه...';
    
    // Reset states
    successOverlay.style.display = 'none';
    failureOverlay.style.display = 'none';

    try {
        const frames = await biometrics.captureSequence(video, 10, 250);
        overlayText.innerText = 'جاري التحقق من الهوية...';
        
        const response = await fetch('<?= BASE_URL ?>auth/biometricLogin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: email,
                type: 'face',
                images: frames
            })
        });

        const result = await response.json();
        if (result.status === 'success') {
            overlay.style.display = 'none';
            successOverlay.style.display = 'flex';
            setTimeout(() => {
                window.location.href = '<?= BASE_URL ?>categories/index';
            }, 1500);
        } else {
            throw new Error(result.message || 'فشل التحقق من الوجه');
        }
    } catch (error) {
        console.error(error);
        overlay.style.display = 'none';
        failureOverlay.style.display = 'flex';
        failureMsg.innerText = error.message;
        btn.disabled = false;
        btn.innerText = 'إعادة المحاولة';
    } finally {
        // Only stop if they didn't succeed
        if (successOverlay.style.display !== 'flex') {
            // biometrics.stopCamera(); // Keep on for retry? User might prefer it
        }
    }
}

async function loginWithFingerprint(email) {
    const result = await biometrics.authenticateFingerprint();
    if (result.status === 'success') {
        const response = await fetch('<?= BASE_URL ?>auth/biometricLogin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: email,
                type: 'fingerprint',
                id: result.id
            })
        });

        const authResult = await response.json();
        if (authResult.status === 'success') {
            window.location.href = '<?= BASE_URL ?>categories/index';
        } else {
            showLoginStatus(authResult.message, 'danger');
        }
    } else {
        showLoginStatus(result.message, 'danger');
    }
}

function showLoginStatus(message, type) {
    loginStatus.innerText = message;
    loginStatus.className = `alert alert-${type} mt-3`;
    loginStatus.style.display = 'block';
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
