const menuItems = [
  {
    name: "Samay Baji Set",
    category: "Newari",
    price: 445,
    rating: 4.9,
    image_url: "../images/foods/samaybaji.png",
    desc: "Authentic Newari thali with beaten rice, smoked meat, soyabean salad & more.",
  },
  {
    name: "Tamang Thukpa",
    category: "Tamang",
    price: 280,
    rating: 4.8,
    image_url: "../images/foods/thukpa.png",
    desc: "Warm, perfectly spiced Himalayan noodle soup with fresh cut vegetables.",
  },
  {
    name: "Traditional Dhido",
    category: "Magar",
    price: 350,
    rating: 4.7,
    image_url: "../images/foods/dhido.png",
    desc: "Classic Magar set with buckwheat dhido, local greens & spicy pickle.",
  },
  {
    name: "Momo Platter",
    category: "Street Food",
    price: 250,
    rating: 4.9,
    image_url: "../images/foods/momo.png",
    desc: "Authentic and freshly prepared local dish ready for immediate order.",
  },

  {
    name: "Sekuwa Skewers",
    category: "Grill",
    price: 600,
    rating: 4.8,
    image_url: "../images/foods/sekuwa.png",
    desc: "Authentic and freshly prepared local dish ready for immediate order.",
  },

  {
    name: "Bara & Chhoila",
    category: "Newari",
    price: 350,
    rating: 4.6,
    image_url: "../images/foods/bara.png",
    desc: "Authentic and freshly prepared local dish ready for immediate order.",
  },

  {
    name: "Selroti with Aloo",
    category: "Breakfast",
    price: 200,
    rating: 4.4,
    image_url: "../images/foods/selroti.png",
    desc: "Authentic and freshly prepared local dish ready for immediate order.",
  },

  {
    name: "Gundruk Sadheko",
    category: "Himalayan",
    price: 180,
    rating: 4.5,
    image_url: "../images/foods/gundruk.png",
    desc: "Authentic and freshly prepared local dish ready for immediate order.",
  },

  {
    name: "Kwanti Dal",
    category: "Festival",
    price: 320,
    rating: 4.7,
    image_url: "../images/foods/kwantiDal.png",
    desc: "Authentic and freshly prepared local dish ready for immediate order.",
  },
  {
    name: "Chatamari",
    category: "Newari",
    price: 380,
    rating: 4.9,
    image_url: "../images/foods/chatamari.png",
    desc: "Authentic and freshly prepared local dish ready for immediate order.",
  },
];

function getUser() {
  try {
    return JSON.parse(sessionStorage.getItem("sauni_user"));
  } catch {
    return null;
  }
}

function logout() {
  sessionStorage.removeItem("sauni_user");
  location.href = "index.html";
}

// ─── RENDER AUTH NAV ─────────────────────────────────────────────────────────

function renderAuthNav() {
  const user = getUser();
  const profileSection = document.getElementById("profile-section");
  const authSection = document.getElementById("auth-section");

  if (user) {
    // Show profile button, hide login/signup
    profileSection.style.display = "block";
    authSection.style.display = "none";

    // Populate dropdown
    const initial = (user.username || "U").charAt(0).toUpperCase();
    document.getElementById("profile-btn-initial").textContent = initial;
    document.getElementById("pd-username").textContent = user.username;
    document.getElementById("pd-role").textContent = user.role;

    if (user.role === "customer")
      document.getElementById("pd-customer-links").style.display = "block";
    if (user.role === "admin")
      document.getElementById("pd-admin-links").style.display = "block";
  } else {
    profileSection.style.display = "none";
    authSection.style.display = "flex";
  }
}

// ─── RENDER MENU CARDS ────────────────────────────────────────────────────────

function renderMenuCards() {
  const user = getUser();
  const orderHref = user ? "customer/foodlist.html" : "index.html";
  const grid = document.getElementById("clean-grid-el");

  grid.innerHTML = menuItems
    .map(
      (item) => `
        <div class="premium-card reveal-up" onclick="goToPage(event, '${orderHref}')">
            <div class="pm-img-wrap">
                <div class="pm-top-bar">
                    <span class="pm-tag">${escHtml(item.category)}</span>
                    <div class="pm-rating">
                        <svg viewBox="0 0 576 512"><path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                        ${parseFloat(item.rating).toFixed(1)}
                    </div>
                </div>
                <img src="${escHtml(item.image_url)}" 
                     onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600'"
                     alt="${escHtml(item.name)}">
            </div>
            <div class="pm-body">
                <h3>${escHtml(item.name)}</h3>
                <p>${escHtml(item.desc || "Authentic and freshly prepared local dish ready for immediate order.")}</p>
            </div>
            <div class="pm-foot">
                <div class="pm-price"><span>Rs.</span> ${Number(item.price).toLocaleString()}</div>
                <a href="${orderHref}" class="pm-action" onclick="goToPage(event, '${orderHref}')">
                    Order Now
                    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>
        </div>
    `,
    )
    .join("");
}

// Simple HTML escaping to avoid XSS when inserting data strings into innerHTML
function escHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

// ─── HERO INTERACTIVITY ───────────────────────────────────────────────────────

let quantity = 0;
let currentBasePrice = 445;

function updateQty(change) {
  if (quantity + change < 0) return;
  quantity += change;
  document.getElementById("qtyVal").innerText = quantity;
  recalculateTotal();
}

function recalculateTotal() {
  const total = quantity * currentBasePrice;
  document.getElementById("totalPrice").innerText =
    `Rs. ${total.toLocaleString()}`;
}

function pickHeroItem(element, price, imgSrc) {
  document
    .querySelectorAll(".f-pill")
    .forEach((el) => el.classList.remove("active"));
  element.classList.add("active");

  const heroImage = document.getElementById("heroImage");
  heroImage.style.opacity = "0";
  heroImage.style.transform = "translate(50%, -50%) scale(0.9) rotate(-10deg)";

  setTimeout(() => {
    currentBasePrice = price;
    recalculateTotal();
    heroImage.src = imgSrc;
    heroImage.style.opacity = "1";
    heroImage.style.transform = "translate(50%, -50%) scale(1) rotate(0deg)";
  }, 150);
}

function handleOrderNow(event) {
  const user = getUser();
  goToPage(event, user ? "customer/foodlist.html" : "index.html");
}

// ─── SCROLL HELPERS ───────────────────────────────────────────────────────────

function scrollClean(dir) {
  document
    .getElementById("clean-grid-el")
    .scrollBy({ left: dir * 380, behavior: "smooth" });
}

function scrollHeroPills(dir) {
  document
    .getElementById("heroPillWrap")
    .scrollBy({ left: dir * 120, behavior: "smooth" });
}

// ─── PAGE TRANSITION ──────────────────────────────────────────────────────────

function goToPage(e, url) {
  e.preventDefault();
  document.body.classList.add("page-out");
  setTimeout(() => {
    window.location.href = url;
  }, 350);
}

// ─── INIT ─────────────────────────────────────────────────────────────────────

document.addEventListener("DOMContentLoaded", () => {
  renderAuthNav();
  renderMenuCards();
  recalculateTotal();

  // Re-observe cards injected by renderMenuCards()
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) entry.target.classList.add("visible");
      });
    },
    { threshold: 0.15 },
  );

  document.querySelectorAll(".reveal-up").forEach((el) => observer.observe(el));

  // Immediately reveal hero elements
  setTimeout(() => {
    document
      .querySelectorAll(".hero-left, .hero-bowl")
      .forEach((el) => el.classList.add("visible"));
  }, 100);

  // Sticky nav
  window.addEventListener("scroll", () => {
    document
      .getElementById("sticky-nav")
      .classList.toggle("scrolled", window.scrollY > 30);
  });

  // Close profile dropdown when clicking outside
  document.addEventListener("click", () => {
    document.getElementById("profileDrop")?.classList.remove("open");
  });

  // BFCache fix (Safari/Chrome back-button blank page)
  window.addEventListener("pageshow", (e) => {
    if (e.persisted || document.body.classList.contains("page-out")) {
      document.body.classList.remove("page-out");
    }
  });
});
