/* ==============================================
   FoodBot Smart Assistant Logic (Beginner Friendly)
   ============================================== */

const FoodBotAI = {
  isOpen: false, // chat window open or closed

  // All chat messages are stored here
  history: [
    {
      role: "bot",
      text: "Hello! I'm FoodBot. How can I help you today?",
    },
  ],

  // Start when page loads
  init() {
    this.injectFooter(); // add quick buttons
    this.renderMessages(); // show first message
    this.setupListeners(); // setup actions
  },

  // Add quick buttons at bottom
  injectFooter() {
    const win = document.getElementById("aiWindow");
    const footer = document.createElement("div");
    footer.className = "ai-footer";

    footer.innerHTML = `
      <button class="ai-hint-btn" onclick="FoodBotAI.quickPrompt('Recommend something popular')">🔥 Popular</button>
      <button class="ai-hint-btn" onclick="FoodBotAI.quickPrompt('Cheap eats under 300')">💰 Budget</button>
      <button class="ai-hint-btn" onclick="FoodBotAI.quickPrompt('Show me Newari food')">🇳🇵 Newari</button>
    `;

    win.insertBefore(footer, document.querySelector(".ai-input-wrap"));
  },

  // When quick button is clicked
  quickPrompt(text) {
    document.getElementById("aiInput").value = text;
    this.send();
  },

  // Setup actions (open chat, send message)
  setupListeners() {
    const input = document.getElementById("aiInput");
    const sendBtn = document.getElementById("aiSend");
    const trigger = document.getElementById("aiTrigger");

    trigger.addEventListener("click", () => this.toggle()); // open/close
    input.addEventListener("keypress", (e) => {
      if (e.key === "Enter") this.send(); // press Enter
    });
    sendBtn.addEventListener("click", () => this.send()); // click send
  },

  // Open or close chat window
  toggle() {
    this.isOpen = !this.isOpen;
    document.getElementById("aiWindow").classList.toggle("open", this.isOpen);
    if (this.isOpen) document.getElementById("aiInput").focus();
  },

  // Show all messages
  renderMessages() {
    const container = document.getElementById("aiMessages");
    container.innerHTML = "";

    this.history.forEach((m) => {
      const msg = document.createElement("div");
      msg.className = ["ai-msg", m.role].join(" ");

      const text = document.createElement("div");
      text.className = "ai-msg-text";
      text.textContent = m.text;
      msg.appendChild(text);

      if (m.dish) {
        const dishCard = document.createElement("div");
        dishCard.innerHTML = this.renderDishCard(m.dish);
        msg.appendChild(dishCard);
      }

      container.appendChild(msg);
    });

    container.scrollTop = container.scrollHeight; // scroll down
  },

  // Food suggestion card
  renderDishCard(dish) {
    let img = dish.image_url || "assets/img/default-food.jpg";
    const safeName = this.escapeHtml(dish.name);
    const safePrice = this.escapeHtml(String(dish.price));
    const safeDish = JSON.stringify(dish).replace(/'/g, "\\'");

    return `
      <div class="ai-dish-card">
        <div class="ai-dish-img" style="background-image:url('${img}')"></div>
        <div class="ai-dish-info">
          <div class="ai-dish-name">${safeName}</div>
          <div class="ai-dish-price">Rs. ${safePrice}</div>
          <button type="button" class="ai-btn-add" onclick='FoodBotAI.quickAdd(${safeDish})'>Quick Add to Cart</button>
        </div>
      </div>
    `;
  },

  // Escape special characters
  escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  },

  // Send message
  async send() {
    const input = document.getElementById("aiInput");
    const text = input.value.trim();
    if (!text) return;

    this.history.push({ role: "user", text });
    input.value = "";
    this.renderMessages();

    // Show typing dots
    const container = document.getElementById("aiMessages");
    const typing = document.createElement("div");
    typing.className = "typing";
    typing.innerHTML = "<span></span><span></span><span></span>";
    container.appendChild(typing);
    container.scrollTop = container.scrollHeight;

    const sendBtn = document.getElementById("aiSend");
    sendBtn.disabled = true;
    input.disabled = true;

    // Fake bot reply after 1.5s
    setTimeout(() => {
      typing.remove();

      let mockResponse = "Here is a popular item you might enjoy.";
      let mockDish = {
        id: 1,
        name: "Samay Baji Set",
        price: 450,
        image_url: "assets/img/foods/samaybaji.png",
      };

      const lowerText = text.toLowerCase();
      if (
        lowerText.includes("cheap") ||
        lowerText.includes("budget") ||
        lowerText.includes("300")
      ) {
        mockResponse = "Here is a budget option under Rs. 300!";
        mockDish = {
          id: 2,
          name: "Yomari",
          price: 250,
          image_url: "assets/img/foods/Newari/yomari.webp",
        };
      } else if (lowerText.includes("tharu")) {
        mockResponse = "Tharu cuisine suggestion: Dhikri.";
        mockDish = {
          id: 3,
          name: "Dhikri",
          price: 250,
          image_url: "assets/img/foods/Tharu/dhikri.jpg",
        };
      } else if (lowerText.includes("newari")) {
        mockResponse = "Newari cuisine suggestion: Haku Choila.";
        mockDish = {
          id: 4,
          name: "Haku Choila",
          price: 375,
          image_url: "assets/img/foods/Newari/choila.webp",
        };
      }

      this.history.push({ role: "bot", text: mockResponse, dish: mockDish });
      this.renderMessages();

      sendBtn.disabled = false;
      input.disabled = false;
      input.focus();
    }, 1500);
  },

  // Add item to cart
  quickAdd(item) {
    alert("Added '" + item.name + "' to cart!");
  },
};

// Start when page loads
document.addEventListener("DOMContentLoaded", () => FoodBotAI.init());
