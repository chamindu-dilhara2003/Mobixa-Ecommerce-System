document.addEventListener('DOMContentLoaded', () => {
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  const container = document.getElementById('checkout-container');
  const { items, total } = getCartItems();

  if (!items.length) {
    window.location.href = 'cart.html';
    return;
  }

  function renderForm(errors, selectedMethod) {
    selectedMethod = selectedMethod || 'cod';
    container.innerHTML = `
      <h1>Checkout</h1>
      <p><strong>Total: ${formatMoney(total)}</strong></p>
      ${(errors || []).map(er => `<div class="alert error">${escapeHtml(er)}</div>`).join('')}
      <form id="checkout-form">
        <div class="form-group">
          <label>Delivery Address</label>
          <textarea class="form-control" name="address" rows="3" required></textarea>
        </div>
        <div class="form-group">
          <label>City</label>
          <input class="form-control" name="city" required>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input class="form-control" name="phone" required>
        </div>

        <div class="form-group">
          <label>Payment Method</label>
          <div class="payment-options">
            <label class="payment-option ${selectedMethod === 'cod' ? 'active' : ''}">
              <input type="radio" name="payment_method" value="cod" ${selectedMethod === 'cod' ? 'checked' : ''}>
              <span class="payment-icon">💵</span>
              <span class="payment-text">
                <strong>Cash on Delivery</strong>
                <small>Pay when your order arrives</small>
              </span>
            </label>
            <label class="payment-option ${selectedMethod === 'card' ? 'active' : ''}">
              <input type="radio" name="payment_method" value="card" ${selectedMethod === 'card' ? 'checked' : ''}>
              <span class="payment-icon">💳</span>
              <span class="payment-text">
                <strong>Card</strong>
                <small>Pay online with debit/credit card</small>
              </span>
            </label>
          </div>
        </div>

        <div id="card-fields" style="display:${selectedMethod === 'card' ? '' : 'none'}">
          <div class="form-group">
            <label>Card Number</label>
            <input class="form-control" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
          </div>
          <div class="card-row">
            <div class="form-group">
              <label>Expiry (MM/YY)</label>
              <input class="form-control" name="card_expiry" placeholder="MM/YY" maxlength="5">
            </div>
            <div class="form-group">
              <label>CVV</label>
              <input class="form-control" name="card_cvv" placeholder="123" maxlength="4">
            </div>
          </div>
        </div>

        <button class="btn success" type="submit">Place Order</button>
      </form>
    `;

    const cardFields = document.getElementById('card-fields');
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
      radio.addEventListener('change', () => {
        document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
        radio.closest('.payment-option').classList.add('active');
        cardFields.style.display = radio.value === 'card' ? '' : 'none';
      });
    });

    // Auto-format card number as the user types (1234 5678 9012 3456)
    const cardNumberInput = document.querySelector('input[name="card_number"]');
    if (cardNumberInput) {
      cardNumberInput.addEventListener('input', () => {
        const digits = cardNumberInput.value.replace(/\D/g, '').slice(0, 16);
        cardNumberInput.value = digits.replace(/(.{4})/g, '$1 ').trim();
      });
    }

    // Auto-format expiry as MM/YY
    const expiryInput = document.querySelector('input[name="card_expiry"]');
    if (expiryInput) {
      expiryInput.addEventListener('input', () => {
        let digits = expiryInput.value.replace(/\D/g, '').slice(0, 4);
        if (digits.length > 2) digits = digits.slice(0, 2) + '/' + digits.slice(2);
        expiryInput.value = digits;
      });
    }

    document.getElementById('checkout-form').addEventListener('submit', e => {
      e.preventDefault();
      const address = e.target.address.value.trim();
      const city = e.target.city.value.trim();
      const phone = e.target.phone.value.trim();
      const paymentMethod = e.target.payment_method.value;

      if (!address || !city || !phone) {
        renderForm(['Please fill all delivery details.'], paymentMethod);
        return;
      }

      if (paymentMethod === 'card') {
        const cardNumber = e.target.card_number.value.replace(/\s/g, '');
        const cardExpiry = e.target.card_expiry.value.trim();
        const cardCvv = e.target.card_cvv.value.trim();

        if (cardNumber.length !== 16 || !/^\d{2}\/\d{2}$/.test(cardExpiry) || !/^\d{3,4}$/.test(cardCvv)) {
          renderForm(['Please enter valid card details.'], paymentMethod);
          return;
        }
      }

      // Simulate placing the order (no backend/database in this static build)
      const orderId = Math.floor(1000 + Math.random() * 9000);
      const paymentLabel = paymentMethod === 'card' ? 'Card' : 'Cash on Delivery';
      clearCart();

      container.innerHTML = `
        <h1>Checkout</h1>
        <div class="alert success">Order placed successfully. Order #${orderId}</div>
        <p>Payment Method: <strong>${paymentLabel}</strong></p>
        <p>Thank you! We'll deliver to: ${escapeHtml(address)}, ${escapeHtml(city)}.</p>
        <a class="btn" href="index.html">Back to Home</a>
        <a class="btn secondary" href="products.html">Continue Shopping</a>
      `;
    });
  }

  renderForm();
});
