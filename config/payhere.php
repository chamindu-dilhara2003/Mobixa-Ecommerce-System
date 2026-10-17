<?php
/*
 * PayHere settings. Keep the merchant secret private: never put it in a
 * JavaScript file or commit a real secret to a public repository.
 */
return [
    'sandbox' => true,
    'merchant_id' => '1238343',
    'merchant_secret' => 'MjM5Mjc2MTA1MjIxNDk0NjE3NjkxNzY2NjUwMzUyMzQzNTA0MDU2MQ==',
    // Use your public HTTPS address for live payments. PayHere must be able
    // to reach /payhere_notify.php at this address.
    'base_url' => 'http://localhost/mobixa_N/',
];
