<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <!-- Header with Logout -->
    <div class="d-flex justify-between align-center mb-3">
        <h1 class="mb-0">
            <i class="bi bi-folder"></i> تصنيفاتي
        </h1>
        <div>
            <a href="<?= BASE_URL ?>profile/index" class="btn btn-sm btn-outline-primary me-2">
                <i class="bi bi-person-circle"></i>
            </a>
            <a href="<?= BASE_URL ?>auth/logout" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="card-custom mb-3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white;">
        <h3 class="mb-1">مرحباً، <?= htmlspecialchars($user['name']) ?>! 👋</h3>
        <p class="mb-0 opacity-75">لديك <?= count($categories) ?> تصنيف</p>
    </div>

    <!-- Quick Search (Speed Access) -->
    <?php if (!empty($categories)): ?>
    <div class="search-container">
        <div class="search-wrapper">
            <i class="bi bi-search search-icon-custom"></i>
            <input 
                type="text" 
                id="searchCategories" 
                class="search-input-custom" 
                placeholder="ابحث عن تصنيف..."
                autocomplete="off"
            >
        </div>
    </div>
    <?php endif; ?>

    <!-- Categories List -->
    <?php if (empty($categories)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-folder-x"></i>
            </div>
            <h3>لا توجد تصنيفات بعد</h3>
            <p>ابدأ بإنشاء تصنيف جديد لتنظيم مهامك</p>
        </div>
    <?php else: ?>
        <div class="list-custom" id="categoriesList">
            <?php foreach ($categories as $category): ?>
                <div class="list-item" onclick="window.location.href='<?= BASE_URL ?>tasks/index/<?= $category['id'] ?>'">
                    <div class="d-flex justify-between align-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="bi bi-folder-fill text-primary"></i>
                                <?= htmlspecialchars($category['name']) ?>
                            </h4>
                            <small class="text-muted">
                                <i class="bi bi-list-task"></i>
                                <?= $category['task_count'] ?> مهمة
                            </small>
                        </div>
                        <div>
                            <button 
                                class="btn btn-sm btn-outline-primary me-1" 
                                onclick="event.stopPropagation(); editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="event.stopPropagation(); deleteCategory(<?= $category['id'] ?>)"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Floating Action Button -->
    <button class="fab" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-folder-plus"></i> إضافة تصنيف جديد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>categories/create">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="category_name">اسم التصنيف</label>
                        <input 
                            type="text" 
                            id="category_name" 
                            name="name" 
                            class="form-control-custom" 
                            placeholder="مثال: دراسة، عمل، حياة شخصية"
                            required
                        >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil"></i> تعديل التصنيف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editCategoryForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_category_name">اسم التصنيف</label>
                        <input 
                            type="text" 
                            id="edit_category_name" 
                            name="name" 
                            class="form-control-custom" 
                            required
                        >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    filterList('searchCategories', 'categoriesList', '.list-item');
});

function editCategory(id, name) {
    document.getElementById('edit_category_name').value = name;
    document.getElementById('editCategoryForm').action = '<?= BASE_URL ?>categories/edit/' + id;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

function deleteCategory(id) {
    if (confirm('هل أنت متأكد من حذف هذا التصنيف؟ سيتم حذف جميع المهام المرتبطة به.')) {
        window.location.href = '<?= BASE_URL ?>categories/delete/' + id;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
