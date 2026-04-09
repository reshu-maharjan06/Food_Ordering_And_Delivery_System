const STATUSES = ['pending', 'prepared', 'assigned', 'picked_up', 'delivered'];
const STATUS_COLORS = { pending:'#ff9e03', prepared:'#3b82f6', assigned:'#8b5cf6', picked_up:'#f97316', delivered:'#10b981' };
let allOrders = [], drivers = [];

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

async function loadOrders() {
    const r = await fetch('../api.php?action=admin_orders_kanban');
    allOrders = await r.json();
    renderBoard();
    updateStats();
}
async function loadDrivers() {
    const r = await fetch('../api.php?action=admin_drivers');
    drivers = await r.json();
}
function renderBoard() {
    const cols = {
        pending: allOrders.filter(o => o.status === 'pending'),
        prepared: allOrders.filter(o => o.status === 'prepared'),
        active: allOrders.filter(o => ['assigned', 'picked_up'].includes(o.status)),
        delivered: allOrders.filter(o => o.status === 'delivered')
    };
    
    for (const [key, orders] of Object.entries(cols)) {
        const col = document.getElementById('col-' + key);
        const badge = document.getElementById('cnt-' + key);
        if (badge) badge.textContent = orders.length;
        if (col) {
            col.innerHTML = orders.length
                ? orders.map(orderCard).join('')
                : '<div style="text-align:center;padding:2rem;color:#d1d5db;font-size:0.8rem;font-weight:500;">No orders</div>';
        }
    }
}
function orderCard(o) {
    let items = [];
    try { items = JSON.parse(o.items_json); } catch(e) {}
    const itemStr = items.map(i => `${i.qty}× ${i.name}`).join(', ');
    const time = new Date(o.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    return `
        <div class="order-card">
            <div class="oc-top"><span class="oc-id">#${o.id}</span><span class="oc-time">${time}</span></div>
            <div class="oc-name">${o.customer_name || 'Customer'}</div>
            <div class="oc-items">${itemStr}</div>
            ${o.note ? `<div class="oc-note"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>${o.note}</div>` : ''}
            ${o.delivery_person_id ? `<div class="oc-driver"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polyline points="16 8 20 8 23 11 23 16 16 16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>${o.driver_name || 'Driver assigned'}</div>` : ''}
            <div class="oc-foot">
                <span class="oc-amt">Rs. ${parseFloat(o.total_amount).toFixed(0)}</span>
                <div style="display:flex;gap:0.4rem;">
                    ${statusButtons(o)}
                </div>
            </div>
        </div>
    `;
}
function statusButtons(o) {
    const idx = STATUSES.indexOf(o.status);
    let btns = '';
    if (idx < STATUSES.length - 1) {
        const next = STATUSES[idx + 1];
        const label = next.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
        if (next === 'assigned') {
            btns += `<button class="btn btn-xs btn-primary" onclick="openAssignModal(${o.id})" title="Assign Driver">Assign Driver</button>`;
        } else {
            btns += `<button class="btn btn-xs btn-primary" onclick="moveOrder(${o.id},'${next}')">${label}</button>`;
        }
    }
    if (o.status !== 'delivered') {
        btns += `<button class="btn btn-xs btn-danger" onclick="moveOrder(${o.id},'cancelled')" title="Cancel">✕</button>`;
    }
    return btns;
}
async function moveOrder(id, status) {
    const r = await fetch('../api.php?action=transition_order_status', {
        method: 'POST', 
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf() },
        body: JSON.stringify({ order_id: id, status })
    });
    const d = await r.json();
    if (d.success) loadOrders();
    else alert(d.error || 'Failed to update');
}
let pendingAssignId = null;
async function openAssignModal(orderId) {
    pendingAssignId = orderId;
    await loadDrivers();
    const list = document.getElementById('driverList');
    if (!list) return;
    if (!drivers.length) {
        list.innerHTML = '<p style="text-align:center;color:#9ca3af;padding:1rem;">No drivers available</p>';
    } else {
        list.innerHTML = drivers.map(d => `
            <div class="driver-item" onclick="selectDriver(this, ${d.id})">
                <div class="driver-av">${d.username.charAt(0).toUpperCase()}</div>
                <div><div class="driver-name">${d.username}</div><div class="driver-status">Available</div></div>
                <span class="driver-badge">#${d.id}</span>
            </div>
        `).join('');
    }
    openModal('assignModal');
}
function selectDriver(el, driverId) {
    document.querySelectorAll('.driver-item').forEach(d => d.classList.remove('sel'));
    el.classList.add('sel');
    el.dataset.did = driverId;
}
async function confirmAssign() {
    const sel = document.querySelector('.driver-item.sel');
    if (!sel) { alert('Please select a driver.'); return; }
    const driverId = parseInt(sel.dataset.did);
    const r = await fetch('../api.php?action=assign_driver', {
        method: 'POST', 
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf() },
        body: JSON.stringify({ order_id: pendingAssignId, driver_id: driverId })
    });
    const d = await r.json();
    if (d.success) { closeModal('assignModal'); loadOrders(); }
    else alert(d.error || 'Failed to assign');
}
function updateStats() {
    const todayOrders = allOrders.filter(o => {
        const d = new Date(o.created_at);
        const now = new Date();
        return d.toDateString() === now.toDateString();
    });
    const revenue = allOrders.filter(o => o.status === 'delivered').reduce((s, o) => s + parseFloat(o.total_amount), 0);
    const pending = allOrders.filter(o => o.status === 'pending').length;
    const active = allOrders.filter(o => ['assigned', 'picked_up'].includes(o.status)).length;

    const elTod = document.getElementById('stat-done');
    const elRev = document.getElementById('stat-active');
    const elPend = document.getElementById('stat-pending');
    if (elTod) elTod.textContent = todayOrders.length;
    if (elRev) elRev.textContent = active;
    if (elPend) elPend.textContent = pending;
}
function openModal(id) { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }
loadOrders();
setInterval(loadOrders, 10000);
