// ─── AUTH GUARD ──────────────────────────────────
(function () {
  const user = (() => {
    try {
      return JSON.parse(sessionStorage.getItem("sauni_user"));
    } catch {
      return null;
    }
  })();

  const logoutBtn = document.getElementById("logout-link");

  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      sessionStorage.removeItem("sauni_user");
      location.href = "../login/index.html";
    });
  }
})();

// ─── CONFIG ──────────────────────────────────────
const CONFIG = {
  RESTAURANT: {
    lat: 27.71534,
    lng: 85.3144,
    name: "Sauni Kitchen — Thamel",
  },
  POLL_INTERVAL: 4000,
  FIXED_COMMISSION: 70,
  COLORS: {
    rider: "#10b981",
    kitchen: "#ff9e03",
    customer: "#ff3b00",
  },
};

let state = {
  map: null,
  riderPos: [CONFIG.RESTAURANT.lat, CONFIG.RESTAURANT.lng],
  activeOrder: null,
  markers: {
    rider: null,
    kitchen: null,
    customer: null,
  },
  route: null,
  isPolling: false,
  lastStatus: null,
};

// ─── ICONS ───────────────────────────────────────
function riderIcon(size = 40) {
  return L.divIcon({
    html: `
      <div style="
        background:var(--rider);
        width:${size}px;
        height:${size}px;
        border-radius:50%;
        border:4px solid #fff;
        box-shadow:0 10px 30px rgba(0,0,0,0.1);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff
      ">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="3"
          stroke-linecap="round" stroke-linejoin="round">
          <rect x="1" y="3" width="15" height="13"/>
          <polyline points="16 8 20 8 23 11 23 16 16 16"/>
          <circle cx="5.5" cy="18.5" r="2.5"/>
          <circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
      </div>
    `,
    className: "",
    iconSize: [size, size],
    iconAnchor: [size / 2, size / 2],
  });
}

function destIcon(size = 40) {
  return L.divIcon({
    html: `
      <div style="
        background:var(--dest);
        width:${size}px;
        height:${size}px;
        border-radius:50%;
        border:4px solid #fff;
        box-shadow:0 10px 30px rgba(0,0,0,0.1);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff
      ">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="3"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
      </div>
    `,
    className: "",
    iconSize: [size, size],
    iconAnchor: [size / 2, size / 2],
  });
}

function kitchenIcon(size = 40) {
  return L.divIcon({
    html: `
      <div style="
        background:var(--kitchen);
        width:${size}px;
        height:${size}px;
        border-radius:50%;
        border:4px solid #fff;
        box-shadow:0 10px 30px rgba(0,0,0,0.1);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff
      ">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="3"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
          <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
          <line x1="6" y1="1" x2="6" y2="4"/>
          <line x1="10" y1="1" x2="10" y2="4"/>
          <line x1="14" y1="1" x2="14" y2="4"/>
        </svg>
      </div>
    `,
    className: "",
    iconSize: [size, size],
    iconAnchor: [size / 2, size / 2],
  });
}

// ─── MAP ─────────────────────────────────────────
function initMap() {
  if (state.map) return;

  state.map = L.map("map", { zoomControl: false }).setView(state.riderPos, 15);

  L.tileLayer(
    "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
    { attribution: "© OSM" },
  ).addTo(state.map);

  state.markers.kitchen = L.marker(
    [CONFIG.RESTAURANT.lat, CONFIG.RESTAURANT.lng],
    { icon: kitchenIcon() },
  )
    .addTo(state.map)
    .bindPopup(CONFIG.RESTAURANT.name);

  state.markers.rider = L.marker(state.riderPos, {
    icon: riderIcon(),
    draggable: true,
  })
    .addTo(state.map)
    .bindPopup("<b>Your Location</b>");

  state.markers.rider.on("dragend", () => {
    const ll = state.markers.rider.getLatLng();
    state.riderPos = [ll.lat, ll.lng];
    syncPosition();
  });

  setTimeout(() => state.map.invalidateSize(), 500);
}

function recenterMap() {
  if (state.map) state.map.flyTo(state.riderPos, 16);
}

// ─── ROUTE ───────────────────────────────────────
async function updateRoute(from, to, color) {
  if (!state.map) return;

  if (state.route) {
    state.map.removeLayer(state.route);
  }

  try {
    const r = await fetch(
      `https://router.project-osrm.org/route/v1/driving/${from[1]},${from[0]};${to[1]},${to[0]}?overview=full&geometries=geojson`,
    );

    const d = await r.json();

    if (d.routes?.[0]) {
      state.route = L.geoJSON(d.routes[0].geometry, {
        style: {
          color,
          weight: 6,
          opacity: 0.9,
        },
        className: "leaflet-ant-path",
      }).addTo(state.map);
    }
  } catch (e) {
    console.error(e);
  }
}

// ─── GPS ─────────────────────────────────────────
if ("geolocation" in navigator) {
  navigator.geolocation.watchPosition(
    (p) => {
      state.riderPos = [p.coords.latitude, p.coords.longitude];

      if (state.markers.rider) {
        state.markers.rider.setLatLng(state.riderPos);
      }

      syncPosition();
    },
    null,
    { enableHighAccuracy: true },
  );
}

// ─── LOCATION SYNC ───────────────────────────────
function syncPosition() {
  fetch("../api.php?action=update_driver_location", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token":
        document.querySelector('meta[name="csrf-token"]')?.content || "",
    },
    body: JSON.stringify({
      lat: state.riderPos[0],
      lng: state.riderPos[1],
      csrf_token:
        document.querySelector('meta[name="csrf-token"]')?.content || "",
    }),
  });

  if (state.activeOrder) {
    refreshRoute();
  }
}

// ─── POLLING ─────────────────────────────────────
async function poll() {
  if (state.isPolling) return;

  state.isPolling = true;

  try {
    const r = await fetch("../api.php?action=my_assigned_order");

    if (!r.ok) {
      throw new Error(`HTTP error! Status: ${r.status}`);
    }

    const text = await r.text();

    let order = null;

    try {
      order = JSON.parse(text);
    } catch (e) {
      const js = text.indexOf("{");
      const je = text.lastIndexOf("}");

      if (js !== -1 && je !== -1 && je > js) {
        order = JSON.parse(text.substring(js, je + 1));
      } else if (text.trim() === "null" || text.trim() === "") {
        order = null;
      } else {
        throw new Error("Invalid server response format");
      }
    }

    const statusChanged =
      (!state.activeOrder && order) ||
      (state.activeOrder && !order) ||
      (order && order.status !== state.activeOrder?.status);

    state.activeOrder = order;

    renderMission();

    if (statusChanged) {
      if (order) {
        syncMapMarkers(order);
        refreshRoute(true);
      } else {
        clearMapState();
      }
    }

    state.lastStatus = order?.status;
  } catch (e) {
    console.error("Poll Error:", e);
  } finally {
    state.isPolling = false;
  }
}

// ─── MISSION UI ──────────────────────────────────
function renderMission() {
  const wrap = document.getElementById("missionContent");

  if (!state.activeOrder || state.activeOrder.status === "delivered") {
    wrap.innerHTML = `
      <div class="mission-empty">
        <svg width="60" height="60" viewBox="0 0 24 24" fill="none"
          stroke="#d1d5db" stroke-width="1.5"
          stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
        <p>Awaiting assignment…<br>Keep your GPS active.</p>
      </div>
    `;
    return;
  }

  const o = state.activeOrder;

  const items = JSON.parse(o.items_json || "[]")
    .map((i) => `${i.qty}× ${i.name}`)
    .join(", ");

  let subHtml = "";
  let btnHtml = "";

  if (o.status === "assigned") {
    subHtml = `
      <div class="phase-indicator">
        <div class="ph-title">Phase 1: Collection</div>
        <div class="ph-desc">
          Head to the kitchen to collect the order items.
        </div>
      </div>
    `;

    btnHtml = `
      <button class="action-primary btn-pickup"
        onclick="transitionStatus('picked_up')">
        Picked Up
      </button>
    `;
  } else if (o.status === "picked_up") {
    subHtml = `
      <div class="phase-indicator">
        <div class="ph-title">Phase 2: Delivery</div>
        <div class="ph-desc">
          Deliver to <b>${o.customer_name}</b>.
        </div>
      </div>
    `;

    btnHtml = `
      <button class="action-primary btn-deliver"
        onclick="transitionStatus('delivered')">
        Delivered
      </button>
    `;
  }

  wrap.innerHTML = `
    <div class="order-card">
      <div class="oc-id">Order ID #${o.id}</div>
      <div class="oc-customer">${o.customer_name}</div>
      <div class="oc-items">${items}</div>
      <div class="oc-price">Rs. ${o.total_amount}</div>
    </div>

    ${subHtml}
    ${btnHtml}

    ${
      o.note
        ? `
      <div style="
        margin-top:1.5rem;
        font-size:0.8rem;
        color:#888;
        background:#f5f5f5;
        padding:0.8rem 1rem;
        border-radius:12px;
        font-weight:500
      ">
        Note: ${o.note}
      </div>
    `
        : ""
    }
  `;
}

// ─── CUSTOMER MARKER ─────────────────────────────
function syncMapMarkers(o) {
  if (!state.map) return;

  const dest = [parseFloat(o.dest_lat), parseFloat(o.dest_lng)];

  if (!state.markers.customer) {
    state.markers.customer = L.marker(dest, {
      icon: destIcon(),
    })
      .addTo(state.map)
      .bindPopup(`Deliver to: ${o.customer_name}`);
  } else {
    state.markers.customer.setLatLng(dest);
  }
}

// ─── ROUTE REFRESH ───────────────────────────────
async function refreshRoute(fit = false) {
  if (
    !state.map ||
    !state.activeOrder ||
    state.activeOrder.status === "delivered"
  ) {
    clearMapState();
    return;
  }

  const o = state.activeOrder;

  const rider = state.riderPos;

  let dest;
  let color;

  if (o.status === "assigned") {
    dest = [CONFIG.RESTAURANT.lat, CONFIG.RESTAURANT.lng];
    color = CONFIG.COLORS.kitchen;
  } else if (o.status === "picked_up") {
    dest = [parseFloat(o.dest_lat), parseFloat(o.dest_lng)];
    color = CONFIG.COLORS.customer;
  } else {
    clearMapState();
    return;
  }

  updateRoute(rider, dest, color);

  if (fit) {
    state.map.fitBounds([rider, dest], {
      padding: [60, 60],
    });
  }
}

// ─── CLEAR MAP ───────────────────────────────────
function clearMapState() {
  if (state.route) {
    state.map.removeLayer(state.route);
    state.route = null;
  }

  if (state.markers.customer) {
    state.map.removeLayer(state.markers.customer);
    state.markers.customer = null;
  }
}

// ─── STATUS UPDATE ───────────────────────────────
function transitionStatus(s) {
  if (!state.activeOrder) return;

  fetch("../api.php?action=update_order_status", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token":
        document.querySelector('meta[name="csrf-token"]')?.content || "",
    },
    body: JSON.stringify({
      order_id: state.activeOrder.id,
      status: s,
      csrf_token:
        document.querySelector('meta[name="csrf-token"]')?.content || "",
    }),
  })
    .then(async (r) => {
      if (!r.ok) {
        throw new Error(`HTTP error! Status: ${r.status}`);
      }

      const text = await r.text();

      try {
        return JSON.parse(text);
      } catch (e) {
        const js = text.indexOf("{");
        const je = text.lastIndexOf("}");

        if (js !== -1 && je !== -1 && je > js) {
          return JSON.parse(text.substring(js, je + 1));
        }

        throw new Error("Invalid server response format");
      }
    })
    .then((res) => {
      if (res.success) {
        if (s === "delivered") {
          document.getElementById("finalEarned").textContent =
            "Rs. " + CONFIG.FIXED_COMMISSION;

          document.getElementById("finishModal").classList.add("open");

          clearMapState();
          state.activeOrder = null;
        }

        poll();
      } else {
        alert(res.error || "Failed to update order status");
      }
    })
    .catch((err) => {
      console.error("Error transitioning order status:", err);
      alert("Network or server error occurred: " + err.message);
    });
}

// ─── MODAL ───────────────────────────────────────
function closeSuccessModal() {
  document.getElementById("finishModal").classList.remove("open");
}

// ─── TABS ────────────────────────────────────────
function switchTab(t, btn) {
  document
    .querySelectorAll(".nav-tab")
    .forEach((x) => x.classList.remove("active"));

  document
    .querySelectorAll(".pane")
    .forEach((x) => x.classList.remove("active"));

  btn.classList.add("active");

  document.getElementById("pane-" + t).classList.add("active");

  if (t === "earnings") {
    loadEarnings();
  }

  if (state.map) {
    setTimeout(() => state.map.invalidateSize(), 150);
  }
}

// ─── EARNINGS ────────────────────────────────────
async function loadEarnings() {
  const r = await fetch("../api.php?action=driver_earnings");

  const d = await r.json();

  const stats = document.getElementById("statGrid");

  stats.innerHTML = `
    <div class="stat-card">
      <div class="st-val">Rs. ${d.today || 0}</div>
      <div class="st-lbl">Today</div>
    </div>

    <div class="stat-card">
      <div class="st-val">Rs. ${d.week || 0}</div>
      <div class="st-lbl">Weekly</div>
    </div>

    <div class="stat-card">
      <div class="st-val">${d.total_count || 0}</div>
      <div class="st-lbl">Trips</div>
    </div>

    <div class="stat-card">
      <div class="st-val">${d.avg_rating || "—"}</div>
      <div class="st-lbl">Rating</div>
    </div>
  `;

  const list = document.getElementById("historyList");

  if (!d.history?.length) {
    list.innerHTML = `
      <p style="
        text-align:center;
        padding:2rem;
        color:#9ca3af;
        font-size:0.85rem
      ">
        No recent trips found.
      </p>
    `;
    return;
  }

  list.innerHTML = d.history
    .map(
      (h) => `
      <div class="hist-card">
        <div class="hc-info">
          <div class="hc-title">${h.customer_name}</div>
          <div class="hc-date">
            ${new Date(h.created_at).toLocaleDateString()}
          </div>
        </div>

        <div class="hc-amt">Rs. 70</div>
      </div>
    `,
    )
    .join("");
}

// ─── INIT ────────────────────────────────────────
window.onload = () => {
  initMap();
  poll();

  setInterval(poll, CONFIG.POLL_INTERVAL);
};

window.onresize = () => {
  if (state.map) {
    state.map.invalidateSize();
  }
};
