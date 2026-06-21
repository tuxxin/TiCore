<?php
// addons/google-analytics-gsc/config/google-analytics-gsc.php — read via config('google-analytics-gsc').
// All values come from .env (never hardcode credentials).
return [
    // Absolute path to the downloaded service-account JSON key file.
    'sa_json'      => getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: '',

    // GA4 numeric property id (e.g. "123456789" — NOT the "G-" measurement id).
    'ga4_property' => getenv('GA4_PROPERTY_ID') ?: '',

    // Search Console property. Domain properties use "sc-domain:example.com";
    // URL-prefix properties use the full "https://example.com/" string.
    'gsc_site'     => getenv('GSC_SITE') ?: (defined('BASE_URL')
                        ? 'sc-domain:' . parse_url(BASE_URL, PHP_URL_HOST)
                        : ''),

    // OAuth scopes minted into the service-account JWT (read-only).
    'scopes'       => 'https://www.googleapis.com/auth/analytics.readonly https://www.googleapis.com/auth/webmasters.readonly',
];
