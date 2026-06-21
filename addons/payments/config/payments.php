<?php
// addons/payments/config/payments.php — all secrets come from .env (never hardcode).
return [
    'currency'            => getenv('PAYMENTS_CURRENCY') ?: 'usd',
    'ga4_id'              => getenv('PAYMENTS_GA4_ID') ?: (defined('GA4_ID') ? GA4_ID : ''),

    // Stripe
    'stripe_secret'       => getenv('STRIPE_SECRET_KEY') ?: '',
    'stripe_pub'          => getenv('STRIPE_PUB_KEY') ?: '',
    'stripe_webhook'      => getenv('STRIPE_WEBHOOK_SECRET') ?: '',

    // PayPal ('sandbox' or 'live')
    'paypal_client_id'    => getenv('PAYPAL_CLIENT_ID') ?: '',
    'paypal_secret'       => getenv('PAYPAL_SECRET') ?: '',
    'paypal_env'          => getenv('PAYPAL_ENV') ?: 'sandbox',
];
