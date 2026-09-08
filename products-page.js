document.addEventListener('DOMContentLoaded', () => {
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  const params = new URLSearchParams(window.location.search);
  let q = (params.get('q') || '').trim();
  let cat = parseInt(params.get('category') || '0', 10) || 0;

  const searchInput = document.getElementById('search-input');
  if (searchInput) searchInput.value = q;

  function render() {
    // Category chips
    const categoryList = document.getElementById('category-list');
    if (categoryList) {
      const allChip = `<a class="${!cat ? 'active' : ''}" href="products.html">All</a>`;
      const chips = CATEGORIES.map(c =>
        `<a class="${cat === c.id ? 'active' : ''}" href="products.html?category=${c.id}">${escapeHtml(c.name)}</a>`
      ).join('');
      categoryList.innerHTML = allChip + chips;
    }

    // Filter products (mirrors the WHERE clause in products.php)
    let products = PRODUCTS.filter(p => {
      const matchesQuery = !q || p.name.toLowerCase().includes(q.toLowerCase()) || p.description.toLowerCase().includes(q.toLowerCase());
      const matchesCategory = !cat || p.category_id === cat;
      return matchesQuery && matchesCategory;
    }).sort((a, b) => b.id - a.id);

    const grid = document.getElementById('product-grid');
    const emptyState = document.getElementById('empty-state');

    if (!products.length) {
      grid.innerHTML = '';
      emptyState.style.display = '';
      return;
    }
    emptyState.style.display = 'none';

    grid.innerHTML = products.map(p => `
      <article class="product-card">
        <a href="product.html?id=${p.id}"><img loading="lazy" src="${p.image || 'assets/images/placeholder.svg'}" alt=""></a>
        <div class="product-info">
          <p class="muted small">${escapeHtml(findCategoryName(p.category_id))}</p>
          <h3>${escapeHtml(p.name)}</h3>
          <p class="price">${formatMoney(p.price)}</p>
          <p class="muted">${escapeHtml(p.description.length > 80 ? p.description.slice(0, 80) + '...' : p.description)}</p>
          <a class="btn" href="product.html?id=${p.id}">View Product</a>
        </div>
      </article>
    `).join('');
  }

  render();

  const searchForm = document.getElementById('search-form');
  if (searchForm) {
    searchForm.addEventListener('submit', e => {
      e.preventDefault();
      const value = searchInput.value.trim();
      const url = new URL(window.location.href);
      if (value) url.searchParams.set('q', value); else url.searchParams.delete('q');
      url.searchParams.delete('category');
      window.location.href = url.toString();
    });
  }
});
