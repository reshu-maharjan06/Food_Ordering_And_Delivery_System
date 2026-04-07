/**
 * Sauni Admin - Order Control Interactivity
 */

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    ev.target.classList.add('dragging');
}

function drop(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    const draggedElement = document.getElementById(data);
    draggedElement.classList.remove('dragging');
    
    // Find the nearest kanban-column or cards-container
    let target = ev.target;
    while (target && !target.classList.contains('kanban-column')) {
        target = target.parentElement;
    }
    
    if (target) {
        const container = target.querySelector('.cards-container');
        
        // Remove empty state if it exists
        const emptyState = container.querySelector('.empty-state');
        if (emptyState) emptyState.style.display = 'none';
        
        container.appendChild(draggedElement);
        updateCounters();
        syncHeaderStats();
    }
}

function updateCounters() {
    const columns = document.querySelectorAll('.kanban-column');
    columns.forEach(col => {
        const count = col.querySelectorAll('.order-card').length;
        col.querySelector('.column-count').innerText = count;
        
        // Show/hide empty state
        const container = col.querySelector('.cards-container');
        const emptyState = container.querySelector('.empty-state');
        if (emptyState) {
            emptyState.style.display = count === 0 ? 'flex' : 'none';
        }
    });
}

function moveToDelivered(orderId) {
    const order = document.getElementById(orderId);
    const deliveredColumn = document.querySelector('.kanban-column[data-status="delivered"] .cards-container');
    
    if (order && deliveredColumn) {
        deliveredColumn.appendChild(order);
        updateCounters();
        syncHeaderStats();
    }
}

function removeOrder(orderId) {
    const order = document.getElementById(orderId);
    if (order && confirm('Are you sure you want to remove this order from the board?')) {
        order.remove();
        updateCounters();
        syncHeaderStats();
    }
}

function syncHeaderStats() {
    // Pending Stat
    const pendingCount = document.querySelector('.kanban-column[data-status="pending"] .order-card').length;
    document.querySelectorAll('.stat-value')[0].innerText = pendingCount;
    
    // In Progress Stat (Prepared + On the Way)
    const preparedCount = document.querySelector('.kanban-column[data-status="prepared"] .order-card').length;
    const onthewayCount = document.querySelector('.kanban-column[data-status="ontheway"] .order-card').length;
    document.querySelectorAll('.stat-value')[1].innerText = preparedCount + onthewayCount;
    
    // Done Today Stat (Delivered)
    const deliveredCount = document.querySelector('.kanban-column[data-status="delivered"] .order-card').length;
    document.querySelectorAll('.stat-value')[2].innerText = deliveredCount;
}

document.addEventListener('DOMContentLoaded', () => {
    // Initial sync
    updateCounters();
    syncHeaderStats();
    
    console.log('Sauni Admin Dashboard - Workable Version Initialized.');
});
