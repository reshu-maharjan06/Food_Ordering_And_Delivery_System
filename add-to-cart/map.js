window.SauniCart = (function () {
  const STORAGE_KEY = "sauni_cart";

  function get() {
    try {
      return JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || [];
    } catch (e) {
      return [];
    }
  }

  function save(cart) {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    updateCartPills();
  }

  function add(item) {
    const cart = get();
    const existing = cart.find((c) => c.name === item.name);
    if (existing) {
      existing.qty += item.qty || 1;
    } else {
      cart.push({ ...item, qty: item.qty || 1 });
    }
    save(cart);
  }

  function setQty(index, qty) {
    const cart = get();
    if (cart[index]) {
      if (qty <= 0) {
        cart.splice(index, 1);
      } else {
        cart[index].qty = qty;
      }
    }
    save(cart);
  }

  function remove(index) {
    const cart = get();
    cart.splice(index, 1);
    save(cart);
  }

  function clear() {
    sessionStorage.removeItem(STORAGE_KEY);
    updateCartPills();
  }

  function total() {
    return get().reduce((sum, item) => sum + item.price * item.qty, 0);
  }

  function totalQty() {
    return get().reduce((sum, item) => sum + item.qty, 0);
  }

  function updateCartPills() {
    const count = totalQty();
    document.querySelectorAll("[data-cart-count]").forEach((el) => {
      el.textContent = count;
    });
  }

  // Initialize pills on load
  document.addEventListener("DOMContentLoaded", updateCartPills);

  return {
    get,
    save,
    add,
    setQty,
    remove,
    clear,
    total,
    totalQty,
    updateCartPills,
  };
})();
