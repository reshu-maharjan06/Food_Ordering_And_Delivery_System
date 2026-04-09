
// ─── HERO INTERACTIVITY ───────────────────────────────────────────────────────

let quantity = 1;
let currentHeroItem = {
    id: 1,
    name: 'Samay Baji',
    price: 445,
    img: 'assets/img/foods/Newari/samaybaji.png',
    desc: 'The ultimate Newari feast: beaten rice, smoked meat, lentils.'
};

function updateQty(change) {
    const qtyValEl = document.getElementById("qtyVal");
    if (!qtyValEl) return;
    
    if (quantity + change < 1) return;
    quantity += change;
    qtyValEl.innerText = quantity;
    recalculateTotal();
}

function recalculateTotal() {
    const totalEl = document.getElementById("totalPrice");
    if (!totalEl) return;

    const total = quantity * currentHeroItem.price;
    totalEl.innerText = `Rs. ${total.toLocaleString()}`;
}

function pickHeroItem(element, id, price, imgSrc, name, desc) {
    document.querySelectorAll(".f-pill").forEach((el) => el.classList.remove("active"));
    if (element) element.classList.add("active");

    const heroImage = document.getElementById("heroImage");
    if (heroImage) {
        heroImage.style.opacity = "0";
        heroImage.style.transform = "translate(50%, -50%) scale(0.9) rotate(-10deg)";

        setTimeout(() => {
            currentHeroItem = { id, name, price, img: imgSrc, desc };
            recalculateTotal();
            heroImage.src = imgSrc;
            heroImage.style.opacity = "1";
            heroImage.style.transform = "translate(50%, -50%) scale(1) rotate(0deg)";
        }, 150);
    }
}

function handleOrderNow(event) {
    if (typeof openFoodModal === 'function') {
        openFoodModal(currentHeroItem.id, currentHeroItem.price, currentHeroItem.name, currentHeroItem.img, currentHeroItem.desc);
    } else {
        window.location.href = 'customer/browse.php';
    }
}

// ─── SCROLL HELPERS ───────────────────────────────────────────────────────────

function scrollClean(dir) {
    const grid = document.getElementById("clean-grid-el");
    if (grid) {
        grid.scrollBy({ left: dir * 380, behavior: "smooth" });
    }
}

function scrollHeroPills(dir) {
    const wrap = document.getElementById("heroPillWrap");
    if (wrap) {
        wrap.scrollBy({ left: dir * 120, behavior: "smooth" });
    }
}

// ─── INIT ─────────────────────────────────────────────────────────────────────

document.addEventListener("DOMContentLoaded", () => {
    recalculateTotal();

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) entry.target.classList.add("visible");
            });
        },
        { threshold: 0.15 }
    );

    document.querySelectorAll(".reveal-up").forEach((el) => observer.observe(el));

    // Immediately reveal hero elements
    setTimeout(() => {
        document.querySelectorAll(".hero-left, .hero-bowl").forEach((el) => el.classList.add("visible"));
    }, 100);

    // Sticky nav
    window.addEventListener("scroll", () => {
        const nav = document.getElementById("sticky-nav");
        if (nav) {
            nav.classList.toggle("scrolled", window.scrollY > 30);
        }
    });

    // Profile dropdown toggle (for initial design)
    const profBtn = document.getElementById("profBtn");
    const profileDrop = document.getElementById("profileDrop");
    
    if (profBtn && profileDrop) {
        profBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            profileDrop.classList.toggle("open");
        });

        document.addEventListener("click", () => {
            profileDrop.classList.remove("open");
        });
    }
});

// For backward compatibility or mixed logic
function openFoodModal(id, price, name, imgSrc, desc) {
    const modal = document.getElementById('foodModal');
    if (!modal) return;

    document.getElementById('fmTitle').innerText = name;
    document.getElementById('fmDesc').innerText = desc;
    document.getElementById('fmPrice').innerHTML = `<span>Rs.</span> ${price.toLocaleString()}`;
    document.getElementById('fmImg').style.backgroundImage = `url('${imgSrc}')`;

    const addBtn = document.getElementById('fmAddBtn');
    if (addBtn) {
        addBtn.onclick = () => {
            if (typeof SauniCart !== 'undefined') {
                let cat = 'Sauni Select';
                if (imgSrc && imgSrc.includes('/foods/')) {
                    const parts = imgSrc.split('/foods/')[1].split('/');
                    if (parts.length > 1) cat = parts[0];
                }
                SauniCart.add({ id, name, price, image_url: imgSrc, qty: quantity, category: cat, description: desc }, addBtn);
                quantity = 1; 
                if (document.getElementById("qtyVal")) document.getElementById("qtyVal").innerText = quantity;
                recalculateTotal();
                closeFoodModal();
            } else {
                console.error("SauniCart not found!");
            }
        };
    }

    modal.classList.add('open');
}

function closeFoodModal() {
    const modal = document.getElementById('foodModal');
    if (modal) modal.classList.remove('open');
}
