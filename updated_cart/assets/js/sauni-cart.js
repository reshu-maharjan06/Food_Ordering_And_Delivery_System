
const SauniCart = {
  KEY: 'sauni_cart',
  get() { 
      try { 
          let cart = JSON.parse(localStorage.getItem(this.KEY) || '[]'); 
          let modified = false;
          cart.forEach(c => {
              if (!c.image_url || c.image_url === 'undefined') {
                  if (c.name === 'Samay Baji') { 
                      c.image_url = 'assets/img/foods/Newari/samaybaji.png'; 
                      c.category = 'Newari'; 
                      c.description = 'The ultimate Newari feast: beaten rice, smoked meat, lentils.';
                      modified = true; 
                  }
              }
              if (!c.category || c.category === 'undefined' || c.category === 'Sauni Select') {
                  if (c.image_url && c.image_url.includes('/foods/')) {
                      const parts = c.image_url.split('/foods/')[1].split('/');
                      if (parts.length > 1) { c.category = parts[0]; modified = true; }
                  } else if (c.name === 'Samay Baji') {
                      c.category = 'Newari'; 
                      c.description = 'The ultimate Newari feast: beaten rice, smoked meat, lentils.';
                      modified = true;
                  }
              }
          });
          if (modified) this.save(cart);
          return cart;
      } catch { return []; } 
  },
  async syncWithDatabase(callback) {
      const cart = this.get();
      if (!cart.length) return;
      try {
          const resp = await fetch((window.location.pathname.includes('/customer/') ? '../' : '') + 'api.php?action=menu');
          const menu = await resp.json();
          let modified = false;
          cart.forEach(c => {
              const master = menu.find(m => m.id == c.id);
              if (master) {
                  if (c.category !== master.category) { c.category = master.category; modified = true; }
                  if (c.image_url !== master.image_url) { c.image_url = master.image_url; modified = true; }
                  if (c.description !== master.description) { c.description = master.description; modified = true; }
              }
          });
          if (modified) {
              this.save(cart);
              if (typeof callback === 'function') callback();
          }
      } catch (e) { console.warn("Cart sync failed:", e); }
  },
  save(cart) { localStorage.setItem(this.KEY, JSON.stringify(cart)); },
  count() { return this.get().reduce((s,c) => s + c.qty, 0); },
  total() { return this.get().reduce((s,c) => s + c.price * c.qty, 0); },
  add(item, originEl) {
    const cart = this.get();
    const ex = cart.find(c => c.id == item.id);
    const qtyToAdd = item.qty ? parseInt(item.qty) : 1;
    if (ex) {
        ex.qty += qtyToAdd;
        if (item.image_url) ex.image_url = item.image_url;
        if (item.category) ex.category = item.category;
        if (item.description) ex.description = item.description;
    } else {
        cart.push({ 
            id: item.id, 
            name: item.name, 
            price: +item.price, 
            image_url: item.image_url || '', 
            category: item.category || '', 
            description: item.description || '', 
            qty: qtyToAdd 
        });
    }
    this.save(cart);
    this._updatePills();
    if (originEl) flyToCart(originEl, item.image_url);
    SauniToast.add(`🛒 ${item.name}`);
  },
  remove(idx) {
    const cart = this.get();
    if (idx >= 0 && idx < cart.length) cart.splice(idx, 1);
    this.save(cart);
    this._updatePills();
  },
  setQty(idx, qty) {
    const cart = this.get();
    if (idx >= 0 && idx < cart.length) {
      if (qty <= 0) cart.splice(idx, 1);
      else cart[idx].qty = qty;
    }
    this.save(cart);
    this._updatePills();
  },
  clear() { localStorage.removeItem(this.KEY); this._updatePills(); },
  _updatePills() {
    const n = this.count();
    document.querySelectorAll('[data-cart-count]').forEach(el => el.textContent = n);
  }
};
function flyToCart(originEl, imgUrl) {
  const cartPill = document.getElementById('cartPill');
  if (!cartPill) {
    console.warn('flyToCart: cartPill not found');
    return;
  }
  const from = originEl.getBoundingClientRect();
  const to   = cartPill.getBoundingClientRect();
  const fly = document.createElement('div');
  fly.className = 'cart-fly';
  fly.style.cssText = `
    left:${from.left + from.width/2 - 22}px;
    top:${from.top + from.height/2 - 22}px;
    background-image:url('${imgUrl || ''}');
    background-color: var(--o, #ff3b00);
  `;
  document.body.appendChild(fly);
  const dx = to.left + to.width/2 - (from.left + from.width/2);
  const dy = to.top  + to.height/2 - (from.top  + from.height/2);
  fly.style.setProperty('--dx', dx + 'px');
  fly.style.setProperty('--dy', dy + 'px');
  fly.animate([
    { transform: 'translate(0,0) scale(1)', opacity: 1 },
    { transform: `translate(${dx*.4}px, ${dy*.2}px) scale(.7)`, opacity: .9, offset: .4 },
    { transform: `translate(${dx}px, ${dy}px) scale(.1)`, opacity: 0 }
  ], { duration: 650, easing: 'cubic-bezier(.16,1,.3,1)', fill: 'forwards' })
  .onfinish = () => fly.remove();
  cartPill.animate([
    { transform:'scale(1)' },
    { transform:'scale(1.25)' },
    { transform:'scale(1)' }
  ], { duration:400, easing:'ease-out' });
}
const SauniToast = {
  MAX: 4,
  queue: [],
  _ensureStack() {
    if (!document.getElementById('toastStack')) {
      const s = document.createElement('div');
      s.id = 'toastStack';
      document.body.appendChild(s);
    }
    return document.getElementById('toastStack');
  },
  add(msg, icon='') {
    const stack = this._ensureStack();
    const items = stack.querySelectorAll('.toast-item');
    if (items.length >= this.MAX) {
      this._dismiss(items[0]);
    }
    const el = document.createElement('div');
    el.className = 'toast-item';
    el.innerHTML = `<span>${icon || '✅'}</span><span>${msg}</span>`;
    stack.appendChild(el);
    setTimeout(() => el.classList.add('show'), 50);
    setTimeout(() => this._dismiss(el), 3000);
  },
  _dismiss(el) {
    el.classList.add('dismiss');
    el.addEventListener('transitionend', () => el.remove(), { once: true });
  }
};
document.addEventListener('DOMContentLoaded', () => {
  SauniCart._updatePills();
});
