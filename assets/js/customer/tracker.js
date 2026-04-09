
let map = null, rstMarker = null, userMarker = null, drvMarker = null;
const REST_LAT = 27.71534, REST_LNG = 85.31440;
function initMap(ulat, ulng) {
    if(map) return;
    map = L.map('map', {zoomControl: false}).setView([ulat, ulng], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);
    const uIco = L.divIcon({html:'<svg viewBox="0 0 24 24" style="width:32px;fill:#111;stroke:#fff;stroke-width:1;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3" fill="#fff"/></svg>',className:'u-pin',iconSize:[32,32],iconAnchor:[16,32]});
    const rIco = L.divIcon({html:'<svg viewBox="0 0 24 24" style="width:28px;fill:#ff3b00;stroke:#fff;stroke-width:1;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3" fill="#fff"/></svg>',className:'r-pin',iconSize:[28,28],iconAnchor:[14,28]});
    userMarker = L.marker([ulat, ulng], {icon: uIco}).addTo(map);
    rstMarker = L.marker([REST_LAT, REST_LNG], {icon: rIco}).addTo(map);
    map.fitBounds([ [ulat, ulng], [REST_LAT, REST_LNG] ], {padding: [50, 50]});
}
function updateDrv(dlat, dlng) {
    if(!dlat || !dlng || !map) return;
    const dIco = L.divIcon({html:'<svg viewBox="0 0 24 24" style="width:36px;height:36px;fill:#16a34a;stroke:#fff;stroke-width:2;"><circle cx="12" cy="12" r="8"/></svg>',className:'d-pin',iconSize:[36,36],iconAnchor:[18,18]});
    if(!drvMarker) drvMarker = L.marker([dlat, dlng], {icon: dIco}).addTo(map);
    else drvMarker.setLatLng([dlat, dlng]);
}
function renderUI(o) {
    document.getElementById('noAct').style.display='none';
    document.getElementById('mainWrapper').style.display='flex';
    document.getElementById('lblOrdId').innerText = o.id;
    document.getElementById('lblAddr').innerText = o.delivery_address || 'Pinned on Map';
    document.getElementById('lblSubTotal').innerText = `Rs. ${parseFloat(o.total_amount).toFixed(2)}`;
    document.getElementById('lblTotal').innerText = `Rs. ${parseFloat(o.total_amount).toFixed(2)}`;
    const stTop = document.getElementById('lblStatusTop');
    if(o.status === 'delivered') { stTop.className = 'uh-status delivered'; stTop.innerText = 'Delivered'; }
    else {
        stTop.className = 'uh-status delivering';
        stTop.innerText = o.status.replace('_', ' ').replace(/\b\w/g, c=>c.toUpperCase());
    }
    let min = o.status==='picked_up' ? 12 : (o.status==='delivered'?0:35);
    document.getElementById('lblEta').innerText = `${min} Minutes`;
    let itms = [];
    try { itms = JSON.parse(o.items_json); } catch(e){}
    document.getElementById('domItems').innerHTML = itms.map(i=>{
        let imgUrl = i.image_url || '';
        if (imgUrl && !imgUrl.startsWith('http') && !imgUrl.startsWith('../')) imgUrl = '../' + imgUrl;
        let category = 'Special';
        if (imgUrl && imgUrl.includes('/foods/')) {
            const parts = imgUrl.split('/foods/')[1].split('/');
            if (parts.length > 1) category = parts[0];
        }
        return `
        <div class="cart-item">
            <div class="ci-img" style="background-image:url('${imgUrl}')"></div>
            <div class="ci-details">
                <div class="ci-name">${i.name}</div>
                <div class="ci-cat">${category}</div>
                <div class="ci-meta">
                    <div class="ci-qty">Qty : ${i.qty}</div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="ci-price">Rs. ${(i.price*i.qty).toFixed(2)}</div>
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');
    ['pending','prepared','assigned','picked_up','delivered'].forEach(s=>{
        document.getElementById(`ts_${s}`).classList.remove('done');
    });
    let states = ['pending','prepared','assigned','picked_up','delivered'];
    let idx = states.indexOf(o.status);
    if(idx<0) idx=0;
    for(let i=0; i<=idx; i++) {
        document.getElementById(`ts_${states[i]}`).classList.add('done');
    }
    if(o.status === 'delivered') {
        document.getElementById('btnReviewOp').style.display='block';
        fetch(`../api.php?action=check_review&order_id=${o.id}`).then(r=>r.json()).then(d=>{
            if(d.already_reviewed) {
                const b=document.getElementById('btnReviewOp');
                b.innerText = 'Review Submitted ✓'; b.disabled=true; b.style.opacity='0.6';
            }
        });
    }
    if(o.driver_name) {
        document.getElementById('driverPanel').style.display='flex';
        document.getElementById('drName').innerText = o.driver_name;
        document.getElementById('drAv').innerText = o.driver_name.charAt(0).toUpperCase();
        document.getElementById('drId').innerText = o.delivery_person_id;
    }
    initMap(o.dest_lat, o.dest_lng);
    if(o.driver_lat && o.driver_lng) updateDrv(o.driver_lat, o.driver_lng);
}
function poll() {
    fetch('../api.php?action=active_order').then(r=>r.json()).then(o=>{
        if(o && o.id) renderUI(o);
        else document.getElementById('noAct').style.display='flex';
    });
}
poll();
setInterval(poll, 5000);
