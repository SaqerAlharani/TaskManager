<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <!-- Back Button -->
    <a href="<?= BASE_URL ?>categories/index" class="back-button">
        <i class="bi bi-arrow-right"></i> العودة للتصنيفات
    </a>

    <!-- Category Header -->
    <div class="card-custom mb-3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white;">
        <h2 class="mb-1">
            <i class="bi bi-list-task"></i> <?= htmlspecialchars($category['name']) ?>
        </h2>
        <p class="mb-0 opacity-75"><?= count($tasks) ?> مهمة رئيسية</p>
    </div>

    <!-- Quick Search (Speed Access) -->
    <?php if (!empty($tasks)): ?>
    <div class="search-container">
        <div class="search-wrapper">
            <i class="bi bi-search search-icon-custom"></i>
            <input 
                type="text" 
                id="searchTasks" 
                class="search-input-custom" 
                placeholder="ابحث عن مهمة..."
                autocomplete="off"
            >
        </div>
    </div>
    <?php endif; ?>

    <!-- Tasks List -->
    <?php if (empty($tasks)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-clipboard-x"></i>
            </div>
            <h3>لا توجد مهام بعد</h3>
            <p>ابدأ بإنشاء مهمة رئيسية جديدة</p>
        </div>
    <?php else: ?>
        <div class="list-custom" id="tasksList">
            <?php foreach ($tasks as $task): ?>
                <div class="card-custom">
                    <div class="d-flex justify-between align-center mb-2">
                        <h4 class="mb-0">
                            <?= htmlspecialchars($task['title']) ?>
                        </h4>
                        <span class="badge-custom <?= $task['status'] === 'completed' ? 'badge-completed' : 'badge-pending' ?>">
                            <?= $task['status'] === 'completed' ? 'مكتملة' : 'قيد التنفيذ' ?>
                        </span>
                    </div>
                    
                    <?php if ($task['description']): ?>
                        <p class="text-muted text-small mb-2">
                            <?= htmlspecialchars($task['description']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-between align-center">
                        <div>
                            <a href="<?= BASE_URL ?>tasks/subtasks/<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-list-nested"></i>
                                المهام الفرعية (<?= $task['subtask_count'] ?>)
                            </a>
                        </div>
                        <div>
                            <button 
                                class="btn btn-sm btn-outline-success me-1" 
                                onclick="toggleStatus(<?= $task['id'] ?>)"
                                title="تغيير الحالة"
                            >
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <button 
                                class="btn btn-sm btn-outline-primary me-1" 
                                onclick="editTask(<?= $task['id'] ?>, '<?= htmlspecialchars($task['title']) ?>', '<?= htmlspecialchars($task['description'] ?? '') ?>', '<?= $task['status'] ?>')"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="deleteTask(<?= $task['id'] ?>)"
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
    <button class="fab" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle"></i> إضافة مهمة جديدة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>tasks/create/<?= $category['id'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="task_title">عنوان المهمة</label>
                        <input 
                            type="text" 
                            id="task_title" 
                            name="title" 
                            class="form-control-custom" 
                            placeholder="مثال: تطوير موقع PHP"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="task_description">الوصف (اختياري)</label>
                        <textarea 
                            id="task_description" 
                            name="description" 
                            class="form-control-custom" 
                            rows="3"
                            placeholder="وصف تفصيلي للمهمة..."
                        ></textarea>
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

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil"></i> تعديل المهمة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editTaskForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_task_title">عنوان المهمة</label>
                        <input 
                            type="text" 
                            id="edit_task_title" 
                            name="title" 
                            class="form-control-custom" 
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_task_description">الوصف</label>
                        <textarea 
                            id="edit_task_description" 
                            name="description" 
                            class="form-control-custom" 
                            rows="3"
                        ></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_task_status">الحالة</label>
                        <select id="edit_task_status" name="status" class="form-control-custom">
                            <option value="pending">قيد التنفيذ</option>
                            <option value="completed">مكتملة</option>
                        </select>
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
    filterList('searchTasks', 'tasksList', '.card-custom');
});

function editTask(id, title, description, status) {
    document.getElementById('edit_task_title').value = title;
    document.getElementById('edit_task_description').value = description;
    document.getElementById('edit_task_status').value = status;
    document.getElementById('editTaskForm').action = '<?= BASE_URL ?>tasks/edit/' + id;
    new bootstrap.Modal(document.getElementById('editTaskModal')).show();
}

function deleteTask(id) {
    if (confirm('هل أنت متأكد من حذف هذه المهمة؟ سيتم حذف جميع المهام الفرعية المرتبطة بها.')) {
        window.location.href = '<?= BASE_URL ?>tasks/delete/' + id;
    }
}

function toggleStatus(id) {
    window.location.href = '<?= BASE_URL ?>tasks/toggleStatus/' + id;
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
