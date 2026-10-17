document.addEventListener('DOMContentLoaded', () => {

  const form = document.getElementById('cart-form');
  if (!form) return;

  function formatMoney(value) {
    return 'Rs. ' + Number(value).toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  /* ---- Live subtotal + total recalculation (bug fix: previously the
     subtotal/total only updated after a full "Update Cart" page reload). ---- */
  function recalcTotals(changedInput) {
    let total = 0;

    form.querySelectorAll('.qty[data-id]').forEach(input => {
      const qty = Math.max(0, parseInt(input.value, 10) || 0);
      const price = Number(input.dataset.price);
      const sub = qty * price;
      total += sub;

      const cell = document.getElementById('subtotal-' + input.dataset.id);
      if (cell) {
        cell.textContent = formatMoney(sub);
        if (input === changedInput) {
          cell.classList.remove('flash');
          // restart the animation
          void cell.offsetWidth;
          cell.classList.add('flash');
        }
      }
    });

    const totalEl = document.getElementById('cart-total-value');
    if (totalEl) totalEl.textContent = formatMoney(total);
  }

  form.querySelectorAll('.qty[data-id]').forEach(input => {
    input.addEventListener('input', () => recalcTotals(input));
  });

  /* ---- Show the page loader again while Update Cart / Checkout submits,
     so the button click gives immediate feedback instead of feeling stuck. ---- */
  form.addEventListener('submit', () => {
    document.querySelector('.page-loading')?.classList.remove('hide');
  });

  /* ---- Animated row removal instead of an instant jump/reload. ---- */
  document.querySelectorAll('.remove-btn[data-confirm]').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();

      if (!confirm(link.dataset.confirm)) return;

      const row = link.closest('tr');
      const href = link.href;

      if (row) {
        row.classList.add('removing');
        row.addEventListener('transitionend', () => {
          window.location.href = href;
        }, { once: true });
        // Fallback in case the transition event never fires.
        setTimeout(() => { window.location.href = href; }, 400);
      } else {
        window.location.href = href;
      }
    });
  });
});
