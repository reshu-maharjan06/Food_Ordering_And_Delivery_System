
async function loadHistory() {
    const res = await fetch('../api.php?action=customer_orders');
    const data = await res.json();
    const box = document.getElementById('histBody');
    const table = document.getElementById('histTable');
    if (!data.length) { 
        table.style.display = 'none';
        document.querySelector('.pg-wrap').innerHTML += `
            <div class="empty-state">
                <h2>No Adventures Yet.</h2>
                <p>Your culinary journey starts with your first order.</p>
                <a href="../landing.php" class="btn-action">Return to Kitchen</a>
            </div>
        `;
        return; 
    }
    box.innerHTML = data.map(o => {
        const items = JSON.parse(o.items_json||'[]');
        const date = new Date(o.created_at).toLocaleDateString(undefined, {month:'short', day:'numeric', year:'numeric'});
        const isActive = !['delivered', 'cancelled'].includes(o.status);
        const statusClass = o.status === 'delivered' ? 'delivered' : (isActive ? 'active' : '');
        return `
            <tr class="order-row">
                <td class="o-id">#${o.id}</td>
                <td class="o-date">${date}</td>
                <td class="o-items">${items.map(i => `${i.qty}× ${i.name}`).join(', ')}</td>
                <td class="o-total"><span>Rs.</span>${o.total_amount}</td>
                <td><span class="o-status ${statusClass}">${o.status}</span></td>
                <td>
                    ${isActive 
                        ? `<a href="tracker.php" class="btn-action">Track Live</a>` 
                        : (Number(o.has_reviewed) 
                            ? `<span class="o-status delivered">Reviewed</span>`
                            : `<button class="btn-action" style="background:#4b5563" onclick="openReviewModal(${o.id})">Leave Review</button>`)
                    }
                </td>
            </tr>
        `;
    }).join('');
}
let activeOrderId = null, currentStars = 5;
function openReviewModal(oid) {
    activeOrderId = oid;
    currentStars = 5;
    document.getElementById('targetOrderId').textContent = oid;
    document.getElementById('reviewModal').classList.add('open');
    updateStars();
}
function closeReviewModal() {
    document.getElementById('reviewModal').classList.remove('open');
    document.getElementById('revComment').value = '';
}
document.querySelectorAll('.star').forEach(s => {
    s.addEventListener('click', function() {
        currentStars = parseInt(this.dataset.val);
        updateStars();
    });
});
function updateStars() {
    document.querySelectorAll('.star').forEach(s => {
        s.classList.toggle('on', parseInt(s.dataset.val) <= currentStars);
    });
}
async function submitReview() {
    const comment = document.getElementById('revComment').value;
    const res = await fetch('../api.php?action=rate_order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: activeOrderId, rating: currentStars, comment })
    });
    const d = await res.json();
    if (d.success) {
        closeReviewModal();
        loadHistory();
    } else {
        alert(d.error || 'Failed to submit review');
    }
}
loadHistory();
