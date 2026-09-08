document.addEventListener('DOMContentLoaded', () => {
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  const container = document.getElementById('cart-container');
  const flash = document.getElementById('flash-message');

  function showFlash(message) {
    flash.textContent = message;
    flash.style.display = '';
  }

  function render() {
    const { items, total } = getCartItems();

    if (!items.length) {
      container.innerHTML = `
        <div class="empty">
          <h2>Your cart is empty</h2>
          <a class="btn" href="products.html">Shop Now</a>
        </div>
      `;
      return;
    }

    const rows = items.map(({ product, qty, sub }) => `
      <tr data-row-id="${product.id}">
        <td>${escapeHtml(product.name)}</td>
        <td>${formatMoney(product.price)}</td>
        <td><input class="qty" type="number" min="1" max="${product.stock}" data-id="${product.id}" data-price="${product.price}" value="${qty}"></td>
        <td class="subtotal-cell" data-id="${product.id}">${formatMoney(sub)}</td>
        <td><a href="#" class="remove-btn" data-id="${product.id}">Remove</a></td>
      </tr>
    `).join('');

    container.innerHTML = `
      <form id="update-cart-form">
        <div class="table-wrap">
          <table class="table">
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
              <th>Action</th>
            </tr>
            ${rows}
          </table>
        </div>
        <div class="cart-total">
          <h2>Total: <span id="cart-total-value">${formatMoney(total)}</span></h2>
          <div class="cart-actions">
            <button class="btn secondary" type="submit">Update Cart</button>
            <a class="btn" href="checkout.html">Checkout →</a>
          </div>
        </div>
      </form>
    `;

    function recalcTotals() {
      let newTotal = 0;
      document.querySelectorAll('.qty[data-id]').forEach(input => {
        const qty = Math.max(0, parseInt(input.value, 10) || 0);
        const price = Number(input.dataset.price);
        const sub = qty * price;
        newTotal += sub;
        const cell = document.querySelector(`.subtotal-cell[data-id="${input.dataset.id}"]`);
        if (cell) cell.textContent = formatMoney(sub);
      });
      document.getElementById('cart-total-value').textContent = formatMoney(newTotal);
    }

    // Live-update subtotal + total as the user types/changes quantity
    document.querySelectorAll('.qty[data-id]').forEach(input => {
      input.addEventListener('input', recalcTotals);
    });

    document.getElementById('update-cart-form').addEventListener('submit', e => {
      e.preventDefault();
      const qtyMap = {};
      document.querySelectorAll('.qty[data-id]').forEach(input => {
        qtyMap[input.dataset.id] = input.value;
      });
      updateCartQuantities(qtyMap);
      showFlash('Cart updated.');
      render();
    });

    document.querySelectorAll('.remove-btn[data-id]').forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        if (!confirm('Are you sure you want to remove this product from your cart?')) return;
        removeFromCart(link.dataset.id);
        showFlash('Product removed from cart.');
        render();
      });
    });
  }

  render();
});
