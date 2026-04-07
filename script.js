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
    if (!columns.length) return; // Exit if no kanban columns are found
    
    columns.forEach(col => {
        const count = col.querySelectorAll('.order-card').length;
        const columnCountElement = col.querySelector('.column-count');
        if (columnCountElement) columnCountElement.innerText = count;
        
        // Show/hide empty state
        const container = col.querySelector('.cards-container');
        if (container) {
            const emptyState = container.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = count === 0 ? 'flex' : 'none';
            }
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
    const statValues = document.querySelectorAll('.stat-value');
    if (statValues.length < 3) return; // Exit if header stats aren't present
    
    // Pending Stat
    const pendingCol = document.querySelector('.kanban-column[data-status="pending"]');
    if (pendingCol) {
        const pendingCount = pendingCol.querySelectorAll('.order-card').length;
        statValues[0].innerText = pendingCount;
    }
    
    // In Progress Stat (Prepared + On the Way)
    const preparedCol = document.querySelector('.kanban-column[data-status="prepared"]');
    const onthewayCol = document.querySelector('.kanban-column[data-status="ontheway"]');
    if (preparedCol && onthewayCol) {
        const preparedCount = preparedCol.querySelectorAll('.order-card').length;
        const onthewayCount = onthewayCol.querySelectorAll('.order-card').length;
        statValues[1].innerText = preparedCount + onthewayCount;
    }
    
    // Done Today Stat (Delivered)
    const deliveredCol = document.querySelector('.kanban-column[data-status="delivered"]');
    if (deliveredCol) {
        const deliveredCount = deliveredCol.querySelectorAll('.order-card').length;
        statValues[2].innerText = deliveredCount;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Only run Kanban-specific sync if the board exists
    if (document.querySelector('.kanban-board')) {
        updateCounters();
        syncHeaderStats();
    }
    
    console.log('Sauni Admin Dashboard - Core initialized.');
});
