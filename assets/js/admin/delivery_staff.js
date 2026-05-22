
let map = null, markers = {};
let selectedRider = null;
const addressCache = {};
const COLORS = { rider: '#10b981', customer: '#ff3b00', kitchen: '#ff9e03' };
const RESTAURANT = { lat: 27.71534, lng: 85.31440 };

async function getAddress(lat, lng, riderId) {
    const key = `${parseFloat(lat).toFixed(4)},${parseFloat(lng).toFixed(4)}`;
    if (addressCache[key]) return addressCache[key];
    
    // Placeholder while fetching
    addressCache[key] = "Fetching address...";
    
    try {
        const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`, {
            headers: { 'Accept-Language': 'en' }
        });
        const d = await r.json();
        const addr = d.display_name ? d.display_name.split(',').slice(0, 2).join(',') : "Unknown Location";
        addressCache[key] = addr;
        
        // Update the DOM directly if the element exists
        const el = document.querySelector(`.rider-card[data-id="${riderId}"] .rc-pos`);
        if (el) el.textContent = addr;
    } catch (e) {
        addressCache[key] = `Lat: ${parseFloat(lat).toFixed(3)}, Lng: ${parseFloat(lng).toFixed(3)}`;
    }
    return addressCache[key];
}
function riderIcon(initial, isSelected) {
    const bg = isSelected ? '#ff3b00' : '#10b981';
    return L.divIcon({
        html: `<div style="background:${bg};width:36px;height:36px;border-radius:50%;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;">${initial}</div>`,
        className: '',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });
}
function kitchenIcon() {
    return L.divIcon({
        html: `<div style="background:#ff9e03;width:36px;height:36px;border-radius:50%;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;color:#fff;">🍽</div>`,
        className: '',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });
}
function initMap() {
    if (map) return;
    map = L.map('map', { zoomControl: false }).setView([RESTAURANT.lat, RESTAURANT.lng], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '' }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.marker([RESTAURANT.lat, RESTAURANT.lng], { icon: kitchenIcon() }).addTo(map).bindPopup('Sauni Kitchen');
}
async function loadRiders() {
    const r = await fetch('../api.php?action=admin_drivers');
    const drivers = await r.json();
    const list = document.getElementById('riderList');
    if (!list) return;
    list.innerHTML = drivers.map(d => {
        const lat = d.lat ? parseFloat(d.lat) : null;
        const lng = d.lng ? parseFloat(d.lng) : null;
        let posText = "No location signal";
        
        if (lat && lng) {
            const cacheKey = `${lat.toFixed(4)},${lng.toFixed(4)}`;
            if (addressCache[cacheKey]) {
                posText = addressCache[cacheKey];
            } else {
                posText = "Locating...";
                getAddress(lat, lng, d.id); // Trigger background fetch
            }
        }

        return `
            <div class="rider-card ${d.id == selectedRider ? 'sel' : ''}" data-id="${d.id}" onclick="focusRider(${d.id}, '${d.username}', ${lat || RESTAURANT.lat}, ${lng || RESTAURANT.lng})">
                <div class="rc-top">
                    <div class="rc-av avatar">${d.username.charAt(0).toUpperCase()}</div>
                    <div class="rc-name">${d.username}</div>
                    <div class="rc-online" title="Online"></div>
                </div>
                <div class="rc-pos">${posText}</div>
                ${d.active_order_count != null ? `<div class="rc-orders">${d.active_order_count} active order(s)</div>` : ''}
            </div>
        `;
    }).join('') || '<p style="text-align:center;color:#9ca3af;padding:2rem;font-size:0.85rem;">No delivery staff found.</p>';
    drivers.forEach(d => {
        if (!d.lat || !d.lng) return;
        const initial = d.username.charAt(0).toUpperCase();
        const pos = [parseFloat(d.lat), parseFloat(d.lng)];
        if (markers[d.id]) {
            markers[d.id].setLatLng(pos);
            markers[d.id].setIcon(riderIcon(initial, d.id == selectedRider));
        } else {
            markers[d.id] = L.marker(pos, { icon: riderIcon(initial, false) })
                .addTo(map)
                .bindPopup(`<b>${d.username}</b>`);
        }
    });
    const overlay = document.getElementById('mapRiderCount');
    if (overlay) overlay.textContent = drivers.filter(d => d.lat).length;
    const sub = document.getElementById('riderCount');
    if (sub) sub.textContent = drivers.length + ' Total Rider(s)';
}
function focusRider(id, name, lat, lng) {
    selectedRider = id;
    if (map) map.flyTo([lat, lng], 16);
    Object.entries(markers).forEach(([mid, marker]) => {
        const d = { username: name };
        marker.setIcon(riderIcon(name.charAt(0).toUpperCase(), parseInt(mid) === id));
    });
    loadRiders();
}
window.onload = () => {
    initMap();
    loadRiders();
    setInterval(loadRiders, 5000);
};
window.onresize = () => { if (map) map.invalidateSize(); };
