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

  // Wire up logout
  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (e) => {
      e.preventDefault();
      sessionStorage.removeItem("sauni_user");
      location.href = "../login/index.html";
    });
  }
})();

// ─── CART STATE ───────────────────────────────────────────────────────────────

let cart = SauniCart.get();
let selLat = 27.7172,
  selLng = 85.324;

// ─── PANEL RENDERING ──────────────────────────────────────────────────────────

function renderPanel() {
  cart = SauniCart.get();
  const inner = document.getElementById("opInner");
  if (!cart.length) {
    inner.innerHTML = `
            <div class="empty-cart">
                <div style="font-size:3rem;margin-bottom:1.5rem">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                </div>
                <h2>Cart is Empty</h2>
                <a href="foodlist.html" class="btn-place" style="text-decoration:none;display:block;text-align:center">← Browse Menu</a>
            </div>`;
    return;
  }

  const total = SauniCart.total();
  inner.innerHTML = `
        <div class="op-title">Checkout</div>
        <div id="cartItems"></div>
        <div class="cart-total"><span>Total</span><span>Rs. ${total.toFixed(0)}</span></div>
        <div class="form-group" style="margin-top:1.5rem">
            <label class="form-label">Delivery Note</label>
            <textarea id="orderNote" class="form-input form-textarea" placeholder="Extra spicy, no onions..."></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Delivery Address</label>
            <input type="text" id="addrText" class="form-input" placeholder="Pin on map →" readonly>
        </div>
        <div class="err-msg" id="errMsg"></div>
        <button class="btn-place" onclick="placeOrder()">🚀 Place Order</button>`;

  renderCartItems();
}

function renderCartItems() {
  cart = SauniCart.get();
  const wrap = document.getElementById("cartItems");
  if (!wrap) return;

  wrap.innerHTML = cart
    .map(
      (c, i) => `
        <div class="cart-item">
            <div class="ci-img" style="background-image:url('${c.image_url || ""}')"></div>
            <div class="ci-info">
                <div class="ci-name">${c.name}</div>
                <div class="ci-price">Rs. ${(c.price * c.qty).toFixed(0)}</div>
            </div>
            <div class="ci-qty">
                <button class="qb" onclick="cqty(${i}, ${c.qty - 1})">−</button>
                <span class="qv">${c.qty}</span>
                <button class="qb" onclick="cqty(${i}, ${c.qty + 1})">+</button>
            </div>
            <button class="ci-del" onclick="crem(${i})">✕</button>
        </div>`,
    )
    .join("");

  const tot = document.querySelector(".cart-total span:last-child");
  if (tot) tot.textContent = "Rs. " + SauniCart.total().toFixed(0);
}

function cqty(i, q) {
  SauniCart.setQty(i, q);
  renderCartItems();
}
function crem(i) {
  SauniCart.remove(i);
  renderPanel();
}

renderPanel();

// ─── MAP SETUP ────────────────────────────────────────────────────────────────

const map = L.map("map").setView([selLat, selLng], 15);
L.tileLayer(
  "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
  {
    attribution: "© OSM",
  },
).addTo(map);

const pinIcon = L.divIcon({
  html: `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="45" viewBox="0 0 32 48">
               <path d="M16 0C7.163 0 0 7.163 0 16c0 10 16 32 16 32S32 26 32 16C32 7.163 24.837 0 16 0z" fill="#ff3b00"/>
               <circle cx="16" cy="16" r="7" fill="white"/>
           </svg>`,
  className: "bespoke-pin",
  iconSize: [30, 45],
  iconAnchor: [15, 45],
});

const marker = L.marker([selLat, selLng], {
  icon: pinIcon,
  draggable: true,
}).addTo(map);
marker.bindPopup("📍 Your delivery location");

marker.on("dragend", () => {
  const ll = marker.getLatLng();
  selLat = ll.lat;
  selLng = ll.lng;
  reverseGeo(ll.lat, ll.lng);
  document.getElementById("mapHint").style.display = "none";
});

map.on("click", (e) => {
  marker.setLatLng(e.latlng);
  selLat = e.latlng.lat;
  selLng = e.latlng.lng;
  reverseGeo(e.latlng.lat, e.latlng.lng);
  document.getElementById("mapHint").style.display = "none";
});

// Try to use the user's real location
navigator.geolocation?.getCurrentPosition(
  (pos) => {
    selLat = pos.coords.latitude;
    selLng = pos.coords.longitude;
    map.setView([selLat, selLng], 17);
    marker.setLatLng([selLat, selLng]);
    reverseGeo(selLat, selLng);
    document.getElementById("mapHint").style.display = "none";
  },
  () => {},
);

// ─── GEOCODING ────────────────────────────────────────────────────────────────

function reverseGeo(lat, lng) {
  fetch(
    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
    {
      headers: { "User-Agent": "SauniApp" },
    },
  )
    .then((r) => r.json())
    .then((d) => {
      const el = document.getElementById("addrText");
      if (el)
        el.value = d.display_name || `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    })
    .catch(() => {});
}

function searchPlace() {
  const q = document.getElementById("placeSearch").value.trim();
  if (!q) return;

  // ── Connect your backend search endpoint here ──────────────────────────────
  // The original called ../api.php?action=search_place&q=...
  // Replace the URL below with your own API endpoint.
  //
  fetch(`../api.php?action=search_place&q=${encodeURIComponent(q)}`)
    .then((r) => r.json())
    .then((d) => {
      const box = document.getElementById("placeResults");
      box.style.display = "block";
      box.innerHTML = d.length
        ? d
            .map(
              (p) =>
                `<div class="pr-item" onclick="selectPlace(${p.lat}, ${p.lon}, '${p.display_name.replace(/'/g, "&#39;")}')">📍 ${p.display_name}</div>`,
            )
            .join("")
        : '<div class="pr-item" style="color:#9ca3af">No results</div>';
    })
    .catch(() => {});
}

function selectPlace(lat, lng, name) {
  selLat = +lat;
  selLng = +lng;
  marker.setLatLng([selLat, selLng]);
  map.setView([selLat, selLng], 17);

  const a = document.getElementById("addrText");
  if (a) a.value = name;
  const s = document.getElementById("placeSearch");
  if (s) s.value = name.split(",")[0];

  document.getElementById("placeResults").style.display = "none";
  document.getElementById("mapHint").style.display = "none";
}

// Close results when clicking outside
document.addEventListener("click", (e) => {
  if (!e.target.closest(".map-search") && !e.target.closest(".place-results"))
    document.getElementById("placeResults").style.display = "none";
});

// ─── PLACE ORDER ──────────────────────────────────────────────────────────────
// Connects to your backend API. Replace the fetch URL with your real endpoint.
//
// Expected POST body (JSON):
//   { items, total, lat, lng, address, note }
//
// Expected API response:
//   Success: { success: true }
//   Failure: { success: false, error: "message" }

function placeOrder() {
  const err = document.getElementById("errMsg");
  err.style.display = "none";

  const c = SauniCart.get();
  if (!c.length) {
    err.style.display = "block";
    err.textContent = "⚠️ Cart is empty!";
    return;
  }

  fetch("../api.php?action=place_order", {
    method: "POST",
    body: JSON.stringify({
      items: c,
      total: SauniCart.total(),
      lat: selLat,
      lng: selLng,
      address: document.getElementById("addrText")?.value || "Map Pin",
      note: document.getElementById("orderNote")?.value || "",
    }),
  })
    .then((r) => r.json())
    .then((res) => {
      if (res.success) {
        SauniCart.clear();
        location.href = "tracker.html";
      } else {
        err.style.display = "block";
        err.textContent = "⚠️ " + (res.error || "Something went wrong.");
      }
    })
    .catch(() => {
      err.style.display = "block";
      err.textContent = "⚠️ Network error. Please try again.";
    });
}
