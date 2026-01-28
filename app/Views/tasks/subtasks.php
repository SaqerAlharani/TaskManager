<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-custom">
    <!-- Back Button -->
    <a href="<?= BASE_URL ?>tasks/index/<?= $mainTask['category_id'] ?>" class="back-button">
        <i class="bi bi-arrow-right"></i> العودة للمهام
    </a>

    <!-- Main Task Header -->
    <div class="card-custom mb-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <h3 class="mb-1">
            <i class="bi bi-check2-square"></i> <?= htmlspecialchars($mainTask['title']) ?>
        </h3>
        <p class="mb-0 opacity-75"><?= count($subtasks) ?> مهمة فرعية</p>
    </div>


    <!-- Subtasks List -->
    <?php if (empty($subtasks)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-list-ul"></i>
            </div>
            <h3>لا توجد مهام فرعية بعد</h3>
            <p>ابدأ بإضافة مهام فرعية لهذه المهمة</p>
        </div>
    <?php else: ?>
        <div class="list-custom">
            <?php foreach ($subtasks as $subtask): ?>
                <div class="list-item">
                    <div class="d-flex align-center gap-3">
                        <input 
                            type="checkbox" 
                            class="checkbox-custom" 
                            <?= $subtask['status'] === 'completed' ? 'checked' : '' ?>
                            onchange="toggleSubtaskStatus(<?= $subtask['id'] ?>)"
                        >
                        <div class="flex-grow-1">
                            <span class="<?= $subtask['status'] === 'completed' ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= htmlspecialchars($subtask['title']) ?>
                            </span>
                        </div>
                        <button 
                            class="btn btn-sm btn-outline-danger" 
                            onclick="deleteSubtask(<?= $subtask['id'] ?>)"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Add Subtask Form -->
    <div class="card-custom mt-3">
        <form method="POST" action="<?= BASE_URL ?>tasks/createSubtask/<?= $mainTask['id'] ?>" class="d-flex gap-2">
            <input 
                type="text" 
                name="title" 
                class="form-control-custom" 
                placeholder="أضف مهمة فرعية جديدة..."
                required
                style="flex: 1;"
            >
            <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem;">
                <i class="bi bi-plus-lg"></i>
            </button>
        </form>
    </div>
</div>

<script>
function toggleSubtaskStatus(id) {
    window.location.href = '<?= BASE_URL ?>tasks/toggleSubtaskStatus/' + id;
}

function deleteSubtask(id) {
    if (confirm('هل أنت متأكد من حذف هذه المهمة الفرعية؟')) {
        window.location.href = '<?= BASE_URL ?>tasks/deleteSubtask/' + id;
    }
}
</script>

<style>
.gap-3 {
    gap: 1rem;
}
.flex-grow-1 {
    flex-grow: 1;
}
.gap-2 {
    gap: 0.5rem;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
