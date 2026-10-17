document.addEventListener('DOMContentLoaded', () => {

  const form = document.getElementById('checkout-form');
  if (!form) return;

  const cardFields = document.getElementById('card-fields');

  // Payment method switch: highlight the chosen option and show/hide card fields.
  document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', () => {
      document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
      radio.closest('.payment-option')?.classList.add('active');
      if (cardFields) cardFields.style.display = radio.value === 'card' ? '' : 'none';
    });
  });

  // Auto-format card number as the user types (1234 5678 9012 3456).
  const cardNumberInput = form.querySelector('input[name="card_number"]');
  if (cardNumberInput) {
    cardNumberInput.addEventListener('input', () => {
      const digits = cardNumberInput.value.replace(/\D/g, '').slice(0, 16);
      cardNumberInput.value = digits.replace(/(.{4})/g, '$1 ').trim();
    });
  }

  // Auto-format expiry as MM/YY.
  const expiryInput = form.querySelector('input[name="card_expiry"]');
  if (expiryInput) {
    expiryInput.addEventListener('input', () => {
      let digits = expiryInput.value.replace(/\D/g, '').slice(0, 4);
      if (digits.length > 2) digits = digits.slice(0, 2) + '/' + digits.slice(2);
      expiryInput.value = digits;
    });
  }

  // Give the "Place Order" click immediate feedback while the page reloads.
  form.addEventListener('submit', () => {
    document.querySelector('.page-loading')?.classList.remove('hide');
  });
});
