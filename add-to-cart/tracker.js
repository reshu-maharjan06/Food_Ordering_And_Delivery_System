// ─── AUTH GUARD ───────────────────────────────────────────────────────────────
// Replaces: <?php if(($_SESSION['role']??'')!=='customer'){header("Location:../index.php");exit;} ?>

(function () {
  const user = (() => {
    try {
      return JSON.parse(sessionStorage.getItem("sauni_user"));
    } catch {
      return null;
    }
  })();

  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      sessionStorage.removeItem("sauni_user");
      location.href = "../login/login.html";
    });
  }
})();

// ─── CONSTANTS ────────────────────────────────────────────────────────────────

const RESTAURANT = {
  lat: 27.71534,
  lng: 85.3144,
  name: "Sauni Kitchen, Thamel",
};

// ─── MAP INIT ─────────────────────────────────────────────────────────────────

const map = L.map("map").setView([27.7172, 85.324], 14);
L.tileLayer(
  "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
  {
    attribution: "© OSM",
  },
).addTo(map);

// ─── ICON HELPERS ─────────────────────────────────────────────────────────────

function riderIcon(size = 36) {
  return L.divIcon({
    html: `<div style="background:#10b981;width:${size}px;height:${size}px;border-radius:50%;border:3px solid #fff;box-shadow:0 10px 30px rgba(0,0,0,.1);display:flex;align-items:center;justify-content:center;color:#fff">
                   <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                       <rect x="1" y="3" width="15" height="13"/>
                       <polyline points="16 8 20 8 23 11 23 16 16 16"/>
                       <circle cx="5.5" cy="18.5" r="2.5"/>
                       <circle cx="18.5" cy="18.5" r="2.5"/>
                   </svg>
               </div>`,
    className: "",
    iconSize: [size, size],
    iconAnchor: [size / 2, size / 2],
  });
}

function destIcon(size = 36) {
  return L.divIcon({
    html: `<div style="background:#ff3b00;width:${size}px;height:${size}px;border-radius:50%;border:3px solid #fff;box-shadow:0 10px 30px rgba(0,0,0,.1);display:flex;align-items:center;justify-content:center;color:#fff">
                   <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                       <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                       <circle cx="12" cy="10" r="3"/>
                   </svg>
               </div>`,
    className: "",
    iconSize: [size, size],
    iconAnchor: [size / 2, size / 2],
  });
}

function kitchenIcon(size = 36) {
  return L.divIcon({
    html: `<div style="background:#f59e0b;width:${size}px;height:${size}px;border-radius:50%;border:3px solid #fff;box-shadow:0 10px 30px rgba(0,0,0,.1);display:flex;align-items:center;justify-content:center;color:#fff">
                   <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                       <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                       <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                       <line x1="6" y1="1" x2="6" y2="4"/>
                       <line x1="10" y1="1" x2="10" y2="4"/>
                       <line x1="14" y1="1" x2="14" y2="4"/>
                   </svg>
               </div>`,
    className: "",
    iconSize: [size, size],
    iconAnchor: [size / 2, size / 2],
  });
}

// Static kitchen marker
L.marker([RESTAURANT.lat, RESTAURANT.lng], { icon: kitchenIcon() })
  .addTo(map)
  .bindPopup("<b>🍴 " + RESTAURANT.name + "</b>");

// ─── STATE ────────────────────────────────────────────────────────────────────

let destMarker = null;
let driverMarker = null;
let routeLine = null;
let activeOrder = null;
let pollTimer = null;

// ─── STATUS LABELS ────────────────────────────────────────────────────────────

function statusLabel(s) {
  const icon = (d) =>
    `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px;vertical-align:middle">${d}</svg>`;
  return (
    {
      pending:
        icon(
          '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        ) + " Kitchen Preparing",
      prepared: icon('<polyline points="20 6 9 17 4 12"/>') + " Food Ready",
      assigned:
        icon(
          '<rect x="1" y="3" width="15" height="13"/><polyline points="16 8 20 8 23 11 23 16 16 16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        ) + " Rider Heading to Kitchen",
      picked_up:
        icon('<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>') +
        " Out for Delivery!",
      delivered:
        icon(
          '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        ) + " Delivered!",
    }[s] || s
  );
}

// ─── POLLING ──────────────────────────────────────────────────────────────────
// Connects to your backend. Replace the fetch URL with your real endpoint.
//
// Expected response:
//   Active order: { id, status, created_at, items_json, total_amount,
//                   dest_lat, dest_lng, driver_lat, driver_lng, driver_name }
//   No order: null / falsy

function poll() {
  fetch("../api.php?action=active_order")
    .then((r) => r.json())
    .then((order) => {
      if (!order) {
        showNoOrder();
        return;
      }
      const changed = !activeOrder || activeOrder.status !== order.status;
      const locChanged =
        activeOrder &&
        (activeOrder.driver_lat !== order.driver_lat ||
          activeOrder.driver_lng !== order.driver_lng);
      activeOrder = order;
      renderPanel();
      if (changed || locChanged) updateMap(changed);
    })
    .catch(() => {});
}

// ─── NO ORDER STATE ───────────────────────────────────────────────────────────

function showNoOrder() {
  clearInterval(pollTimer);
  document.getElementById("statusPanel").innerHTML = `
        <div class="no-order">
            <div class="no-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <h2 class="no-title">No Active Order</h2>
            <p style="color:#9ca3af;font-weight:600">Track your delivery here after ordering.</p>
            <a href="foodlist.html" class="btn-order">Browse Menu →</a>
        </div>`;

  if (destMarker) {
    map.removeLayer(destMarker);
    destMarker = null;
  }
  if (driverMarker) {
    map.removeLayer(driverMarker);
    driverMarker = null;
  }
  if (routeLine) {
    map.removeLayer(routeLine);
    routeLine = null;
  }
}

// ─── PANEL RENDERING ──────────────────────────────────────────────────────────

function renderPanel() {
  const o = activeOrder;
  const items = JSON.parse(o.items_json || "[]");
  const steps = ["pending", "prepared", "assigned", "picked_up", "delivered"];
  const stepNames = [
    "Order Received",
    "Preparing Food",
    "Rider Assigned",
    "Out for Delivery",
    "Delivered 🎉",
  ];
  const curIdx = steps.indexOf(o.status);

  document.getElementById("statusPanel").innerHTML = `
        <div class="sp-head">
            <div class="sp-title">Order #${o.id}</div>
            <div class="sp-sub">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                ${new Date(o.created_at).toLocaleDateString()} · ${items.length} items
            </div>
            <div class="status-badge"><div class="pulse"></div>${statusLabel(o.status)}</div>
        </div>

        <div class="sp-body">
            <div class="order-summary">
                ${items
                  .map(
                    (i) => `
                    <div class="oi-row">
                        <span><span class="oi-qty">${i.qty}×</span> ${i.name}</span>
                        <span>Rs. ${i.price * i.qty}</span>
                    </div>`,
                  )
                  .join("")}
                <div class="oi-total"><span>Total</span><span>Rs. ${o.total_amount}</span></div>
            </div>

            <ul class="steps">
                ${stepNames
                  .map(
                    (n, i) => `
                    <li class="step ${i < curIdx ? "done" : i === curIdx ? "active" : ""}">${n}</li>`,
                  )
                  .join("")}
            </ul>

            ${
              o.driver_name
                ? `
            <div style="margin-top:2.5rem;background:#111;padding:1.2rem;border-radius:16px;display:flex;align-items:center;gap:1.2rem;color:#fff;box-shadow:0 15px 35px rgba(0,0,0,.15);">
                <div style="width:48px;height:48px;background:var(--o);color:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:#9ca3af;letter-spacing:1px;margin-bottom:2px">Your Rider</div>
                    <div style="font-weight:700;font-size:1.15rem;font-family:'Outfit'">${o.driver_name}</div>
                </div>
            </div>`
                : ""
            }
        </div>

        <div class="sp-footer" style="${o.status === "delivered" ? "" : "display:none"}">
            <a href="rating.html?id=${o.id}" style="text-decoration:none;">
                <button class="btn-rate">⭐ Leave a Review</button>
            </a>
        </div>`;
}

// ─── MAP UPDATE ───────────────────────────────────────────────────────────────

async function updateMap(fit) {
  const o = activeOrder;
  if (!o) return;

  // Destination marker
  if (!destMarker) {
    destMarker = L.marker([o.dest_lat, o.dest_lng], { icon: destIcon() })
      .addTo(map)
      .bindPopup("<b>📍 Destination</b>");
  }

  // Rider marker
  if (o.driver_lat && o.driver_lng) {
    const ll = [parseFloat(o.driver_lat), parseFloat(o.driver_lng)];

    if (!driverMarker) {
      driverMarker = L.marker(ll, { icon: riderIcon(42) })
        .addTo(map)
        .bindPopup("<b>🛵 Rider</b>");
    } else {
      driverMarker.setLatLng(ll);
    }

    // Route line
    if (routeLine) map.removeLayer(routeLine);
    const to =
      o.status === "assigned"
        ? RESTAURANT
        : { lat: o.dest_lat, lng: o.dest_lng };

    try {
      const r = await fetch(
        `https://router.project-osrm.org/route/v1/driving/${ll[1]},${ll[0]};${to.lng},${to.lat}?overview=full&geometries=geojson`,
      );
      const d = await r.json();
      if (d.routes?.[0]) {
        routeLine = L.geoJSON(d.routes[0].geometry, {
          style: { color: "#ff3b00", weight: 5, opacity: 0.8 },
        }).addTo(map);
      }
    } catch (e) {}

    if (fit) {
      map.fitBounds(
        [[o.dest_lat, o.dest_lng], ll, [RESTAURANT.lat, RESTAURANT.lng]],
        { padding: [60, 60] },
      );
    }
  }
}

// ─── START POLLING ────────────────────────────────────────────────────────────

window.addEventListener("resize", () => map.invalidateSize());
setTimeout(() => map.invalidateSize(), 500);

poll();
pollTimer = setInterval(poll, 4000);
