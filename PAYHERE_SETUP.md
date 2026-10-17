# PayHere setup

1. Open `config/payhere.php` and enter the Merchant ID and Merchant Secret from your PayHere dashboard. Keep the secret private.
2. For testing, leave `sandbox` as `true`. For live payments, set it to `false` and set `base_url` to your public HTTPS site address, for example `https://shop.example.lk`.
3. In PayHere, request approval for the same domain. The Merchant Secret is specific to that domain/app.
4. `payhere_notify.php` must be reachable from the public internet. A `localhost` address can display the sandbox payment page from your computer, but PayHere cannot send the payment confirmation to it.

The integration posts customers to PayHere’s hosted checkout page. It never collects or stores card numbers or CVVs in MobiXa. Payment confirmation is accepted only after the PayHere callback signature is verified.
