const FoodBotAI = {
    isOpen: false,
    STORAGE_KEY: 'foodbot_chat_history',

    _defaultHistory() {
        return [
            { role: 'bot', text: "Hello! I'm FoodBot, your smart food assistant. How can I help you discover something delicious today?" }
        ];
    },

    loadHistory() {
        try {
            const raw = localStorage.getItem(this.STORAGE_KEY);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed) && parsed.length > 0) return parsed;
            }
        } catch (e) {}
        return this._defaultHistory();
    },

    saveHistory() {
        try {
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify(this.history));
        } catch (e) {}
    },

    clearHistory() {
        if (!confirm('Clear all chat history? This cannot be undone.')) return;
        localStorage.removeItem(this.STORAGE_KEY);
        this.history = this._defaultHistory();
        this.renderMessages();
        this._showToast('Chat history cleared.');
    },

    _showToast(msg) {
        const t = document.createElement('div');
        t.className = 'ai-toast';
        t.textContent = msg;
        document.getElementById('ai-assistant-root').appendChild(t);
        setTimeout(() => t.classList.add('ai-toast-show'), 10);
        setTimeout(() => { t.classList.remove('ai-toast-show'); setTimeout(() => t.remove(), 300); }, 2500);
    },

    init() {
        this.history = this.loadHistory();
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
        const input   = document.getElementById('aiInput');
        const sendBtn = document.getElementById('aiSend');
        const trigger = document.getElementById('aiTrigger');
        const clearBtn = document.getElementById('aiClearBtn');

        trigger.addEventListener('click', () => this.toggle());
        input.addEventListener('keypress', (e) => { if (e.key === 'Enter') this.send(); });
        sendBtn.addEventListener('click', () => this.send());
        if (clearBtn) clearBtn.addEventListener('click', () => this.clearHistory());
    },

    toggle() {
        this.isOpen = !this.isOpen;
        document.getElementById('aiWindow').classList.toggle('open', this.isOpen);
        if (this.isOpen) {
            document.getElementById('aiInput').focus();
            const container = document.getElementById('aiMessages');
            setTimeout(() => { container.scrollTop = container.scrollHeight; }, 50);
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
        let img = dish.image_url || 'assets/img/default-food.jpg';
        if (img && !img.startsWith('http') && !img.startsWith('../')) {
            const isCustomer = window.location.pathname.includes('/customer/');
            img = (isCustomer ? '../' : '') + img;
        }
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
        const text  = input.value.trim();
        if (!text) return;

        this.history.push({ role: 'user', text });
        input.value = '';
        this.renderMessages();
        this.saveHistory();

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

            typing.remove();

            if (!resp.ok) {
                const errText = await resp.text();
                let errMsg = 'Server error';
                try { const errData = JSON.parse(errText); errMsg = errData.error || errMsg; } catch(e) {}
                this.history.push({ role: 'bot', text: 'Assistant error: ' + errMsg });
                this.renderMessages();
                this.saveHistory();
                return;
            }

            const textResponse = await resp.text();
            let data;
            try {
                data = JSON.parse(textResponse);
            } catch (err) {
                const js = textResponse.indexOf('{');
                const je = textResponse.lastIndexOf('}');
                if (js !== -1 && je !== -1 && je > js) {
                    data = JSON.parse(textResponse.substring(js, je + 1));
                } else {
                    throw new Error("Invalid response format");
                }
            }

            this.history.push({ role: 'bot', text: data.response || "I couldn't process that.", dish: data.suggested_item });
            this.renderMessages();
            this.saveHistory();

        } catch (e) {
            typing.remove();
            this.history.push({ role: 'bot', text: 'Something went wrong. Please check your connection.' });
            this.renderMessages();
            this.saveHistory();
        }
    },

    quickAdd(item) {
        if (typeof SauniCart !== 'undefined') {
            SauniCart.add(item);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => FoodBotAI.init());
