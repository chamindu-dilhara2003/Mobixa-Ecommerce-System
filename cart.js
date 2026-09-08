/* MobiXa static demo cart
   Replaces the PHP $_SESSION['cart'] logic with localStorage so the cart
   works with no backend/database. Cart shape: { "<productId>": qty, ... } */

const CART_KEY = 'mobixa-cart';

function getCart() {
  try {
    return JSON.parse(localStorage.getItem(CART_KEY)) || {};
  } catch (e) {
    return {};
  }
}

function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartBadge();
}

function cartCount() {
  const cart = getCart();
  return Object.values(cart).reduce((sum, qty) => sum + Number(qty), 0);
}

function addToCart(productId, qty) {
  const product = findProduct(productId);
  if (!product) return;
  const cart = getCart();
  const current = cart[productId] || 0;
  cart[productId] = Math.min(current + qty, product.stock);
  saveCart(cart);
}

function updateCartQuantities(qtyMap) {
  const cart = getCart();
  Object.keys(qtyMap).forEach(id => {
    const product = findProduct(id);
    const qty = Number(qtyMap[id]);
    if (!product || qty <= 0) {
      delete cart[id];
    } else {
      cart[id] = Math.min(qty, product.stock);
    }
  });
  saveCart(cart);
}

function removeFromCart(productId) {
  const cart = getCart();
  delete cart[productId];
  saveCart(cart);
}

function clearCart() {
  saveCart({});
}

function getCartItems() {
  const cart = getCart();
  const items = [];
  let total = 0;
  Object.keys(cart).forEach(id => {
    const product = findProduct(id);
    if (!product) return;
    const qty = cart[id];
    const sub = qty * product.price;
    total += sub;
    items.push({ product, qty, sub });
  });
  return { items, total };
}

function updateCartBadge() {
  const badge = document.querySelector('.nav-links .badge');
  if (badge) badge.textContent = cartCount();
}

document.addEventListener('DOMContentLoaded', updateCartBadge);
