/* FoodBot Smart Assistant Logic */
const FoodBotAI = {
    isOpen: false,
    history: [
        { role: 'bot', text: "Hello! I'm FoodBot, your smart food assistant. How can I help you discover something delicious today?" }
    ],

    init() {
        this.injectFooter();
        this.renderMessages();
        this.setupListeners();
    },

    injectFooter() {
        const win = document.getElementById('aiWindow');
        const footer = document.createElement('div');
        footer.className = 'ai-footer';
        footer.innerHTML = `
            <button class="ai-hint-btn" onclick="FoodBotAI.quickPrompt('Recommend something popular')">🔥 Popular</button>
            <button class="ai-hint-btn" onclick="FoodBotAI.quickPrompt('Cheap eats under 300')">💰 Budget</button>
            <button class="ai-hint-btn" onclick="FoodBotAI.quickPrompt('Show me Newari food')">🇳🇵 Newari</button>
        `;
        win.insertBefore(footer, document.querySelector('.ai-input-wrap'));
    },

    quickPrompt(text) {
        document.getElementById('aiInput').value = text;
        this.send();
    },

    setupListeners() {
        const input = document.getElementById('aiInput');
        const sendBtn = document.getElementById('aiSend');
        const trigger = document.getElementById('aiTrigger');

        trigger.addEventListener('click', () => this.toggle());

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.send();
        });

        sendBtn.addEventListener('click', () => this.send());
    },

    toggle() {
        this.isOpen = !this.isOpen;
        document.getElementById('aiWindow').classList.toggle('open', this.isOpen);
        if (this.isOpen) {
            document.getElementById('aiInput').focus();
        }
    },

    renderMessages() {
        const container = document.getElementById('aiMessages');
        container.innerHTML = this.history.map(m => `
            <div class="ai-msg ${m.role}">
                ${m.text}
                ${m.dish ? this.renderDishCard(m.dish) : ''}
            </div>
        `).join('');
        container.scrollTop = container.scrollHeight;
    },

    renderDishCard(dish) {
        // Fix image path for customer directory
        let img = dish.image_url || 'assets/img/default-food.jpg';
        if (img && !img.startsWith('http') && !img.startsWith('../')) img = '../' + img;

        return `
            <div class="ai-dish-card">
                <div class="ai-dish-img" style="background-image:url('${img}')"></div>
                <div class="ai-dish-info">
                    <div class="ai-dish-name">${dish.name}</div>
                    <div class="ai-dish-price">Rs. ${dish.price}</div>
                    <button class="ai-btn-add" onclick='FoodBotAI.quickAdd(${JSON.stringify(dish)})'>Quick Add to Cart</button>
                </div>
            </div>
        `;
    },

    async send() {
        const input = document.getElementById('aiInput');
        const text = input.value.trim();
        if (!text) return;

        // Add user message
        this.history.push({ role: 'user', text });
        input.value = '';
        this.renderMessages();

        // Show typing indicator
        const container = document.getElementById('aiMessages');
        const typing = document.createElement('div');
        typing.className = 'typing';
        typing.innerHTML = '<span></span><span></span><span></span>';
        container.appendChild(typing);
        container.scrollTop = container.scrollHeight;

        try {
            const apiPath = (window.location.pathname.includes('/customer/') ? '../' : '') + 'api.php?action=ai_chat';
            const resp = await fetch(apiPath, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content 
                },
                body: JSON.stringify({ message: text })
            });
            
            // Remove typing indicator
            typing.remove();

            if (!resp.ok) {
                const errText = await resp.text();
                let errMsg = "Server error";
                try { 
                    const errData = JSON.parse(errText);
                    errMsg = errData.error || errMsg;
                } catch(e) {}
                this.history.push({ role: 'bot', text: "Assistant error: " + errMsg });
                this.renderMessages();
                return;
            }

            const data = await resp.json();
            this.history.push({ role: 'bot', text: data.response, dish: data.suggested_item });
            this.renderMessages();
        } catch (e) {
            typing.remove();
            console.error("AI Chat Error:", e);
            this.history.push({ role: 'bot', text: "Something went wrong. Please check your connection." });
            this.renderMessages();
        }
    },

    quickAdd(item) {
        if (typeof SauniCart !== 'undefined') {
            SauniCart.add(item);
            // Optional: Close assistant after adding
            // this.toggle();
        } else {
            console.error("SauniCart not found");
        }
    }
};

document.addEventListener('DOMContentLoaded', () => FoodBotAI.init());
