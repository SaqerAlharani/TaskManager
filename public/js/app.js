/**
 * Task Manager - Custom JavaScript
 */

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-custom');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Confirm before delete
function confirmDelete(message) {
    return confirm(message || 'هل أنت متأكد من الحذف؟');
}

// Instant filtering for lists (Speed feature)
function filterList(inputId, listContainerId, itemSelector) {
    const input = document.getElementById(inputId);
    const container = document.getElementById(listContainerId);
    if (!input || !container) return;

    input.addEventListener('input', function() {
        const filter = input.value.toLowerCase().trim();
        const items = container.querySelectorAll(itemSelector);
        let visibleCount = 0;

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Toggle empty state if no results
        const emptyState = document.getElementById('searchEmptyState');
        if (visibleCount === 0 && filter !== '') {
            if (!emptyState) {
                const div = document.createElement('div');
                div.id = 'searchEmptyState';
                div.className = 'empty-state';
                div.innerHTML = '<i class="bi bi-search"></i><p>لا توجد نتائج تطابق بحثك</p>';
                container.appendChild(div);
            }
        } else if (emptyState) {
            emptyState.remove();
        }
    });
}
