    <?php if (\App\Helpers\Session::isLoggedIn()): ?>
    <!-- Bottom Navigation for Mobile Speed -->
    <nav class="bottom-nav d-md-none">
        <a href="<?= BASE_URL ?>categories/index" class="bottom-nav-item <?= ($currentPage ?? '') == 'categories' ? 'active' : '' ?>">
            <i class="bi bi-grid-fill"></i>
            <span>الرئيسية</span>
        </a>
        <a href="<?= BASE_URL ?>profile/index" class="bottom-nav-item <?= ($currentPage ?? '') == 'profile' ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i>
            <span>حسابي</span>
        </a>
        <a href="<?= BASE_URL ?>auth/logout" class="bottom-nav-item">
            <i class="bi bi-box-arrow-right"></i>
            <span>خروج</span>
        </a>
    </nav>
    <?php endif; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>js/app.js"></script>
</body>
</html>
