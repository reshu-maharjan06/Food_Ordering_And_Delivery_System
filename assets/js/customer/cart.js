
function renderCart() {
    const cart = SauniCart.get();
    const wrap = document.getElementById('cartItems');
    if (!cart.length) {
        wrap.innerHTML = `<div style="text-align:center; padding:2rem 0; color:#9ca3af; font-size:0.9rem;">Your cart is empty.</div>`;
        document.getElementById('stVal').innerText = 'Rs. 0';
        document.getElementById('totVal').innerText = 'Rs. 0';
        return;
    }
    let html = '';
    let total = 0;
    cart.forEach((c, i) => {
        total += (c.price * c.qty);
        let imgUrl = (!c.image_url || c.image_url === 'undefined') ? '' : c.image_url;
        if (imgUrl && !imgUrl.startsWith('http') && !imgUrl.startsWith('../')) {
            imgUrl = '../' + imgUrl;
        }
        let category = c.category || 'Sauni';
        
        html += `
            <div class="cart-item">
                <div class="ci-img" style="background-image:url('${imgUrl}')"></div>
                <div class="ci-details">
                    <div class="ci-name">${c.name}</div>
                    <div class="ci-cat" style="color:var(--o); font-weight:600; font-size:0.85rem; letter-spacing:0.5px;">${category.toUpperCase()}</div>
                    <div class="ci-meta">
                        <div class="ci-qty">
                            Qty: 
                            <button class="ci-qty-btn" onclick="SauniCart.setQty(${i}, ${c.qty-1}); renderCart();">−</button>
                            ${c.qty}
                            <button class="ci-qty-btn" onclick="SauniCart.setQty(${i}, ${c.qty+1}); renderCart();">+</button>
                        </div>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div class="ci-price">Rs. ${(c.price * c.qty).toFixed(2)}</div>
                            <button title="Remove Item" style="background:#fee2e2; border:none; color:#ef4444; cursor:pointer; padding:6px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" onclick="SauniCart.remove(${i}); renderCart();">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
    });
    wrap.innerHTML = html;
    document.getElementById('stVal').innerText = `Rs. ${total.toFixed(2)}`;
    document.getElementById('totVal').innerText = `Rs. ${total.toFixed(2)}`;
}
renderCart();
SauniCart.syncWithDatabase(renderCart);
let selLat = 27.7172, selLng = 85.3240;
const map = L.map('map', {zoomControl: false}).setView([selLat, selLng], 15);
L.control.zoom({ position: 'bottomright' }).addTo(map);
L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution:'' }).addTo(map);
const pinIcon = L.divIcon({
    html:`<svg viewBox="0 0 24 24" style="width:36px;height:36px; fill:#111; stroke:#fff; stroke-width:1;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3" fill="#fff"/></svg>`,
    className:'bespoke-pin', iconSize:[36,36], iconAnchor:[18,36]
});
const marker = L.marker([selLat, selLng], { icon: pinIcon, draggable: true }).addTo(map);
function updateLoc(ll) {
    selLat = ll.lat; selLng = ll.lng;
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${selLat}&lon=${selLng}`)
        .then(r=>r.json()).then(d=>{ document.getElementById('addrText').value=d.display_name||`${selLat.toFixed(5)},${selLng.toFixed(5)}`; })
        .catch(()=>{ document.getElementById('addrText').value=`${selLat.toFixed(5)}, ${selLng.toFixed(5)}`; });
    document.getElementById('mapHint').style.opacity = '0';
}
marker.on('dragend', () => updateLoc(marker.getLatLng()));
map.on('click', e => { marker.setLatLng(e.latlng); updateLoc(e.latlng); });
navigator.geolocation?.getCurrentPosition(pos => {
    map.setView([pos.coords.latitude, pos.coords.longitude], 17);
    marker.setLatLng([pos.coords.latitude, pos.coords.longitude]);
    updateLoc(marker.getLatLng());
}, () => {});
function searchPlace() {
    const q = document.getElementById('placeSearch').value.trim(); if(!q) return;
    fetch(`../api.php?action=search_place&q=${encodeURIComponent(q)}`).then(r=>r.json()).then(d=>{
        const box = document.getElementById('placeResults');
        box.style.display = 'block';
        box.innerHTML = d.length ? d.map(p=>`<div class="pr-item" onclick="selectPlace(${p.lat},${p.lon},'${p.display_name.replace(/'/g,"&#39;")}')">${p.display_name}</div>`).join('')
            : '<div class="pr-item" style="color:#9ca3af; text-align:center;">No results found</div>';
    });
}
function selectPlace(lat,lng,n) {
    marker.setLatLng([lat,lng]); map.setView([lat,lng],17);
    document.getElementById('addrText').value=n;
    document.getElementById('placeSearch').value=n.split(',')[0];
    document.getElementById('placeResults').style.display='none';
    selLat=lat; selLng=lng;
}
function placeOrder() {
    const err = document.getElementById('errMsg');
    err.style.display='none';
    const c = SauniCart.get();
    if(!c.length) { err.style.display='block'; err.innerText='Cart is empty!'; return; }
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('../api.php?action=place_order',{
        method:'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body:JSON.stringify({ items:c, total:SauniCart.total(), lat:selLat, lng:selLng, address:document.getElementById('addrText').value, note:document.getElementById('orderNote').value })
    }).then(r=>r.json()).then(res=>{
        if(res.success){ SauniCart.clear(); location.href='tracker.php'; }
        else { err.style.display='block'; err.innerText=res.error||'Failed to place order.'; }
    });
}
