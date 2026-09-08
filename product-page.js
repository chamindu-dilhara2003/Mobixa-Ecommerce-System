document.addEventListener('DOMContentLoaded', () => {
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  const params = new URLSearchParams(window.location.search);
  const id = parseInt(params.get('id') || '0', 10);
  const p = findProduct(id);
  const container = document.getElementById('product-container');

  if (!p) {
    container.innerHTML = '<div class="empty"><h2>Product not found</h2><a class="btn" href="products.html">Back to Products</a></div>';
    return;
  }

  document.title = p.name + ' - MobiXa';
  document.getElementById('page-title').textContent = p.name + ' - MobiXa';

  container.innerHTML = `
    <div class="detail-grid">
      <div>
        <img loading="lazy" class="detail-image" src="${p.image || 'assets/images/placeholder.svg'}">
      </div>
      <div>
        <p class="muted">${escapeHtml(findCategoryName(p.category_id))}</p>
        <h1>${escapeHtml(p.name)}</h1>
        <p class="price">${formatMoney(p.price)}</p>
        <p>${escapeHtml(p.description)}</p>
        <p><strong>Stock:</strong> ${p.stock}</p>
        ${p.stock > 0 ? `
          <form id="add-to-cart-form">
            <label>Quantity</label>
            <input class="qty" type="number" id="qty-input" value="1" min="1" max="${p.stock}">
            <button class="btn" type="submit">Add to Cart</button>
          </form>
        ` : '<p class="alert error">Out of stock</p>'}
      </div>
    </div>
  `;

  const form = document.getElementById('add-to-cart-form');
  if (form) {
    form.addEventListener('submit', e => {
      e.preventDefault();
      const qty = Math.max(1, parseInt(document.getElementById('qty-input').value, 10) || 1);
      addToCart(p.id, qty);
      window.location.href = 'cart.html';
    });
  }
});
