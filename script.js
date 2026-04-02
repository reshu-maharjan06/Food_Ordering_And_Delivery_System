// ─────────────────────────────────────────────
//  Sauni – Menu Data & Logic
// ─────────────────────────────────────────────

const categories = [
  { id: "all", label: "All" },
  { id: "newari", label: "Newari" },
  { id: "tharu", label: "Tharu" },
  { id: "tamang", label: "Tamang" },
  { id: "gurung", label: "Gurung" },
  { id: "raikirat", label: "Rai / Kirat" },
  { id: "limbu", label: "Limbu" },
  { id: "sherpa", label: "Sherpa" },
  { id: "thakali", label: "Thakali" },
  { id: "magar", label: "Magar" },
  { id: "beverages", label: "Beverages" },
];

const dishes = [

  // ── Newari ──────────────────────────────────
  {
    id: "n1", category: "newari", label: "Newari",
    name: "Samay Baji Set",
    desc: "Beaten rice, spicy buffalo choila, boiled egg, black soybeans, ginger & traditional achar on a dark wooden platter.",
    price: "Rs. 450.00",
    img: "images/newari/samay_baji.jpg",
  },
  {
    id: "n2", category: "newari", label: "Newari",
    name: "Haku Choila",
    desc: "Charred buffalo mixed with mustard oil, garlic, chili and spices. Rustic, oily and bold a true Newari classic.",
    price: "Rs. 380.00",
    img: "images/newari/choila.webp",
  },
  {
    id: "n3", category: "newari", label: "Newari",
    name: "Chatamari",
    desc: "A thin, crispy rice crepe (Chatamari) folded and served on a traditional brass plate, paired with a small bowl of spicy achar, set on a richly carved wooden table with warm, rustic lighting.",
    price: "Rs. 250.00",
    img: "images/newari/chatamari.jpg",
  },
  {
    id: "n4", category: "newari", label: "Newari",
    name: "Yomari",
    desc: "Soft handmade rice flour dumplings filled with traditional chaku and sesame. One opened to show the filling.",
    price: "Rs. 180.00",
    img: "images/newari/yomari.jpg",
  },
  {
    id: "n5", category: "newari", label: "Newari",
    name: "Bara (Wo)",
    desc: "Thick lentil pancake with natural uneven edges and a traditional egg topping a beloved Newari street snack.",
    price: "Rs. 150.00",
    img: "images/newari/bara.jpg",
  },

  // ── Tharu ───────────────────────────────────
  {
    id: "t1", category: "tharu", label: "Tharu",
    name: "Ghonghi Curry",
    desc: "Freshwater snail curry with rich, rustic gravy served in a traditional clay bowl.",
    price: "Rs. 350.00",
    img: "images/tharu/ghonghi_curry.jpg",
  },
  {
    id: "t2", category: "tharu", label: "Tharu",
    name: "Dhikri",
    desc: "Steamed rice dumplings with slightly imperfect handmade shapes, served on bamboo mat with chutney.",
    price: "Rs. 200.00",
    img: "images/tharu/dhikri.jpg",
  },
  {
    id: "t3", category: "tharu", label: "Tharu",
    name: "Freshwater River Fish Curry",
    desc: "River fish cooked with bones in a rich traditional gravy, served on clay dish over banana leaf.",
    price: "Rs. 400.00",
    img: "images/tharu/fish_curry.jpg",
  },
  {
    id: "t4", category: "tharu", label: "Tharu",
    name: "Chichar",
    desc: "Authentic sun-dried pork preparation with rough texture, served simply on a wooden board.",
    price: "Rs. 220.00",
    img: "images/tharu/Chichar.png",
  },
  {
    id: "t5", category: "tharu", label: "Tharu",
    name: "Sidhara",
    desc: "Traditional fermented fish ingredient served in a clay plate with an earthy, rustic background.",
    price: "Rs. 180.00",
    img: "images/tharu/sidhara.jpg",
  },

  // ── Tamang ──────────────────────────────────
  {
    id: "ta1", category: "tamang", label: "Tamang",
    name: "Tamang Khapse",
    desc: "Deep-fried twisted pastry with irregular handmade shapes, served on a wooden tray. A festive Tamang treat.",
    price: "Rs. 150.00",
    img: "images/tamang/khapse.jpg",
  },
  {
    id: "ta2", category: "tamang", label: "Tamang",
    name: "Buckwheat Roti with Pork",
    desc: "Traditional buckwheat roti with rough texture, served alongside authentic pork curry in a clay bowl.",
    price: "Rs. 380.00",
    img: "images/tamang/buckwheat_roti.jpg",
  },
  {
    id: "ta3", category: "tamang", label: "Tamang",
    name: "Mountain Dhindo",
    desc: "Thick millet porridge with authentic texture, served with gundruk and achar on a stone-surface clay plate.",
    price: "Rs. 280.00",
    img: "images/tamang/dhindo.jpg",
  },
  {
    id: "ta4", category: "tamang", label: "Tamang",
    name: "Yangben Faaksa",
    desc: "Pork with wild mushrooms and earthy tones, served in a rustic bowl with no modern over styling.",
    price: "Rs. 320.00",
    img: "images/tamang/yangben_faaksa.jpg",
  },
  {
    id: "ta5", category: "tamang", label: "Tamang",
    name: "Kinema Curry",
    desc: "Fermented soybean curry with visible texture and a rich oil layer, served traditionally.",
    price: "Rs. 260.00",
    img: "images/tamang/kinema_curry.jpg",
  },

  // ── Gurung ──────────────────────────────────
  {
    id: "g1", category: "gurung", label: "Gurung",
    name: "Gurung Thali Set",
    desc: "Rice, lentils, vegetables and achar arranged in authentic Gurung tradition not overly stylized.",
    price: "Rs. 480.00",
    img: "images/gurung/thali.jpg",
  },
  {
    id: "g2", category: "gurung", label: "Gurung",
    name: "Kukura ko Achar",
    desc: "Spicy chicken achar with natural oil and rough texture, served on a metal or wooden plate.",
    price: "Rs. 320.00",
    img: "images/gurung/kukura_achar.jpg",
  },
  {
    id: "g3", category: "gurung", label: "Gurung",
    name: "Fiddlehead Fern Sauté",
    desc: "Sautéed niuro (fiddlehead) with natural green tones, minimal rustic plating and garlic mustard oil.",
    price: "Rs. 240.00",
    img: "images/gurung/fiddlehead_fern.jpg",
  },
  {
    id: "g4", category: "gurung", label: "Gurung",
    name: "Kodo ko Roti",
    desc: "Traditional millet flatbread with rough, uneven surface — served simply, earthy and nourishing.",
    price: "Rs. 160.00",
    img: "images/gurung/kodo_roti.jpg",
  },
  {
    id: "g5", category: "gurung", label: "Gurung",
    name: "Mohi Chop",
    desc: "Crispy local snack with slightly uneven handmade shapes and a natural, unfussy presentation.",
    price: "Rs. 200.00",
    img: "images/gurung/mohi_chop.jpg",
  },

  // ── Rai / Kirat ─────────────────────────────
  {
    id: "r1", category: "raikirat", label: "Rai / Kirat",
    name: "Wachipa",
    desc: "Dark, authentic ritual dish with traditional texture, served on a rustic plate with minimal styling.",
    price: "Rs. 350.00",
    img: "images/raikirat/wachipa.jpg",
  },
  {
    id: "r2", category: "raikirat", label: "Rai / Kirat",
    name: "Pig Leg Achaar",
    desc: "Spicy pork leg achar with oily, rough texture and traditional Rai-style plating.",
    price: "Rs. 300.00",
    img: "images/raikirat/pig_leg.jpg",
  },
  {
    id: "r3", category: "raikirat", label: "Rai / Kirat",
    name: "Rayo ko Saag with Pork",
    desc: "Simple authentic mustard green dish slow-cooked with pork pieces, served in a clay bowl.",
    price: "Rs. 340.00",
    img: "images/raikirat/rayo_pork.jpg",
  },
  {
    id: "r4", category: "raikirat", label: "Rai / Kirat",
    name: "Yakthung Thupka",
    desc: "Traditional noodle soup with visible steam and simple bowl presentation no over styling.",
    price: "Rs. 320.00",
    img: "images/raikirat/thupka.jpg",
  },
  {
    id: "r5", category: "raikirat", label: "Rai / Kirat",
    name: "Churpi Soup",
    desc: "Traditional hardened cheese melted into a thick, warming broth in a rustic bowl.",
    price: "Rs. 300.00",
    img: "images/raikirat/churpi_soup.jpg",
  },

  // ── Limbu ───────────────────────────────────
  {
    id: "l1", category: "limbu", label: "Limbu",
    name: "Pork Ribs with Bamboo Shoot",
    desc: "Authentic sour pork ribs cooked with fermented bamboo shoot naturally plated.",
    price: "Rs. 370.00",
    img: "images/limbu/pork_bamboo.jpg",
  },
  {
    id: "l2", category: "limbu", label: "Limbu",
    name: "Yangben Soup",
    desc: "Wild mushroom soup with earthy tones and a simple, unfussy bowl presentation.",
    price: "Rs. 280.00",
    img: "images/limbu/yangben_soup.jpg",
  },
  {
    id: "l3", category: "limbu", label: "Limbu",
    name: "Akabare Khorsani Achar",
    desc: "Bright red Himalayan chilli pickle with natural texture and minimal rustic styling.",
    price: "Rs. 120.00",
    img: "images/limbu/akabare_achar.jpg",
  },
  {
    id: "l4", category: "limbu", label: "Limbu",
    name: "Phokso ko Jhol",
    desc: "Organ meat soup with a realistic hearty broth texture traditional and bold.",
    price: "Rs. 320.00",
    img: "images/limbu/phokso.jpg",
  },
  {
    id: "l5", category: "limbu", label: "Limbu",
    name: "Sekuwa Buffalo",
    desc: "Grilled buffalo skewers with charcoal texture, served simply with achar.",
    price: "Rs. 380.00",
    img: "images/limbu/sekuwa_buffalo.jpg",
  },

  // ── Sherpa ──────────────────────────────────
  {
    id: "sh1", category: "sherpa", label: "Sherpa",
    name: "Sherpa Stew (Shyakpa)",
    desc: "Thick traditional stew with realistic meat and potato ingredients in a simple, unpretentious bowl.",
    price: "Rs. 350.00",
    img: "images/sherpa/stew.jpg",
  },
  {
    id: "sh2", category: "sherpa", label: "Sherpa",
    name: "Tsampa Porridge",
    desc: "Barley flour porridge with plain and authentic mountain presentation the fuel of the high Himalayas.",
    price: "Rs. 220.00",
    img: "images/sherpa/tsampa.jpg",
  },
  {
    id: "sh3", category: "sherpa", label: "Sherpa",
    name: "Yak Sukuti",
    desc: "Dried yak meat with natural rough texture, placed on a wooden board. True high-altitude fare.",
    price: "Rs. 420.00",
    img: "images/sherpa/yak_sukuti.jpg",
  },
  {
    id: "sh4", category: "sherpa", label: "Sherpa",
    name: "Sherpa Thukpa",
    desc: "Simple noodle soup with visible steam, served in an authentic bowl with minimal styling.",
    price: "Rs. 320.00",
    img: "images/sherpa/thukpa.jpg",
  },
  {
    id: "sh5", category: "sherpa", label: "Sherpa",
    name: "Serkam Achar",
    desc: "Traditional turmeric pickle in clay bowl with an earthy background. Bright and pungent.",
    price: "Rs. 150.00",
    img: "images/sherpa/serkam.jpg",
  },

  // ── Thakali ─────────────────────────────────
  {
    id: "th1", category: "thakali", label: "Thakali",
    name: "Thakali Thali Set",
    desc: "Authentic full meal set with metal thali rice, dal, vegetables and achar arranged traditionally.",
    price: "Rs. 550.00",
    img: "images/thakali/thali.jpg",
  },
  {
    id: "th2", category: "thakali", label: "Thakali",
    name: "Kanchemba",
    desc: "Traditional Thakali dish with wild garlic and Sichuan pepper rustic, aromatic and authentic.",
    price: "Rs. 300.00",
    img: "images/thakali/kanchemb.jpg",
  },
  {
    id: "th3", category: "thakali", label: "Thakali",
    name: "Dhopra Soup",
    desc: "Simple traditional soup in a clay bowl earthy, warming and minimal.",
    price: "Rs. 260.00",
    img: "images/thakali/dhopra.jpg",
  },
  {
    id: "th4", category: "thakali", label: "Thakali",
    name: "Gyang-to",
    desc: "Authentic roasted corn preparation with Mustang butter and salt minimal styling.",
    price: "Rs. 200.00",
    img: "images/thakali/gyangto.jpg",
  },
  {
    id: "th5", category: "thakali", label: "Thakali",
    name: "Phopke",
    desc: "Traditional highland snack simple, realistic and unpretentious.",
    price: "Rs. 180.00",
    img: "images/thakali/phopke.jpg",
  },

  // ── Magar ───────────────────────────────────
  {
    id: "m1", category: "magar", label: "Magar",
    name: "Batuk Bara",
    desc: "Traditional fried lentil balls with uneven handmade shape, served on a wooden plate.",
    price: "Rs. 150.00",
    img: "images/magar/batuk.jpg",
  },
  {
    id: "m2", category: "magar", label: "Magar",
    name: "Sutkeri Kukhura ko Jhol",
    desc: "Postpartum chicken soup with light, herb-rich broth in a simple clay bowl.",
    price: "Rs. 360.00",
    img: "images/magar/sutkeri.jpg",
  },
  {
    id: "m3", category: "magar", label: "Magar",
    name: "Fried Tarul",
    desc: "Rough-cut wild yam pieces, golden and crispy, simply plated.",
    price: "Rs. 180.00",
    img: "images/magar/tarul.jpg",
  },
  {
    id: "m4", category: "magar", label: "Magar",
    name: "Karkalo ko Achar",
    desc: "Taro leaf pickle with natural earthy tones, sesame and tomato.",
    price: "Rs. 130.00",
    img: "images/magar/karkalo.jpg",
  },
  {
    id: "m5", category: "magar", label: "Magar",
    name: "Frog Curry (Paha)",
    desc: "Traditional rustic frog curry a bold Magar delicacy with no modern styling.",
    price: "Rs. 400.00",
    img: "images/magar/frog.jpg",
  },

  // ── Beverages ───────────────────────────────
  {
    id: "bv1", category: "beverages", label: "Beverages",
    name: "Kathmandu Chilled Beer",
    desc: "Cold beer glass with condensation, realistic bar setup.",
    price: "Rs. 300.00",
    img: "images/beverages/beer.jpg",
  },
  {
    id: "bv2", category: "beverages", label: "Beverages",
    name: "Mustang Coffee",
    desc: "Black coffee in simple ceramic cup, natural setting.",
    price: "Rs. 150.00",
    img: "images/beverages/coffee.jpg",
  },
  {
    id: "bv3", category: "beverages", label: "Beverages",
    name: "Tongba",
    desc: "Traditional wooden vessel with straw, authentic look.",
    price: "Rs. 250.00",
    img: "images/beverages/tongba.jpg",
  },
  {
    id: "bv4", category: "beverages", label: "Beverages",
    name: "Chilled Mohi",
    desc: "Buttermilk in steel or glass, simple clean setup.",
    price: "Rs. 120.00",
    img: "images/beverages/Mohi.jpg",
  },
  {
    id: "bv5", category: "beverages", label: "Beverages",
    name: "Himalayan Apple Juice",
    desc: "Fresh juice with natural apple slices, realistic presentation.",
    price: "Rs. 180.00",
    img: "images/beverages/apple_juice.jpg",
  },
];

// ─────────────────────────────────────────────
//  State
// ─────────────────────────────────────────────

let activeCategory = "all";
let searchQuery = "";
let cartCount = 0;

// ─────────────────────────────────────────────
//  Build one card
// ─────────────────────────────────────────────

function buildCard(dish) {
  const fallback = `https://placehold.co/600x450/fff4e2/ff9e03?text=${encodeURIComponent(dish.name)}`;
  return `
    <div class="food-card" data-id="${dish.id}" data-category="${dish.category}">
      <div class="img-container">
        <img
          src="${dish.img}"
          alt="${dish.name}"
          loading="lazy"
          onerror="this.src='${fallback}'"
        />
      </div>
      <div class="card-content">
        <span class="tag">${dish.label}</span>
        <h4 class="dish-title">${dish.name}</h4>
        <p class="dish-desc">${dish.desc}</p>
        <div class="card-footer">
          <span class="price">${dish.price}</span>
          <button class="add-btn" data-id="${dish.id}" aria-label="Add ${dish.name} to cart">
            <i class="fa-solid fa-plus"></i>
          </button>
        </div>
      </div>
    </div>`;
}

// ─────────────────────────────────────────────
//  Render grid
// ─────────────────────────────────────────────

function renderGrid() {
  const grid = document.getElementById("food-grid");
  const count = document.getElementById("results-count");

  const q = searchQuery.toLowerCase();

  const visible = dishes.filter((d) => {
    const matchCat = activeCategory === "all" || d.category === activeCategory;
    const matchQ = !q
      || d.name.toLowerCase().includes(q)
      || d.desc.toLowerCase().includes(q)
      || d.label.toLowerCase().includes(q);
    return matchCat && matchQ;
  });

  const word = visible.length === 1 ? "dish" : "dishes";
  count.textContent = `${visible.length} ${word} found`;

  if (visible.length === 0) {
    grid.innerHTML = `
      <div class="empty-state">
        <i class="fa-solid fa-bowl-food"></i>
        <p>No dishes match your search.</p>
      </div>`;
    return;
  }

  grid.innerHTML = visible.map(buildCard).join("");
}

// ─────────────────────────────────────────────
//  Render sidebar
// ─────────────────────────────────────────────

function buildSidebar() {
  const ul = document.getElementById("cuisine-list");

  ul.innerHTML = categories.map((cat) => {
    const cnt = cat.id === "all"
      ? dishes.length
      : dishes.filter((d) => d.category === cat.id).length;

    return `
      <li class="${cat.id === activeCategory ? "active" : ""}" data-cat="${cat.id}">
        <span>${cat.label}</span>
        <span class="count-badge">${cnt}</span>
      </li>`;
  }).join("");

  ul.querySelectorAll("li").forEach((li) => {
    li.addEventListener("click", () => {
      activeCategory = li.dataset.cat;
      buildSidebar();
      renderGrid();
    });
  });
}

// ─────────────────────────────────────────────
//  Cart counter
// ─────────────────────────────────────────────

function updateCartBadge() {
  const badge = document.querySelector(".cart-badge");
  if (badge) badge.textContent = cartCount;
}

// ─────────────────────────────────────────────
//  Boot
// ─────────────────────────────────────────────

document.addEventListener("DOMContentLoaded", () => {
  buildSidebar();
  renderGrid();

  // Search
  document.getElementById("search-input").addEventListener("input", (e) => {
    searchQuery = e.target.value.trim();
    renderGrid();
  });

  // Profile dropdown
  const profileToggle = document.getElementById("profileDropdownToggle");
  const dropdownMenu = document.getElementById("dropdownMenu");

  profileToggle.addEventListener("click", (e) => {
    dropdownMenu.classList.toggle("show");
    e.stopPropagation();
  });

  document.addEventListener("click", () => {
    dropdownMenu.classList.remove("show");
  });

  // Add to cart (delegated)
  document.getElementById("food-grid").addEventListener("click", (e) => {
    const btn = e.target.closest(".add-btn");
    if (!btn) return;
    cartCount++;
    updateCartBadge();

    // Quick visual feedback
    btn.style.background = "var(--primary)";
    btn.style.color = "#fff";
    setTimeout(() => {
      btn.style.background = "";
      btn.style.color = "";
    }, 300);
  });
});
