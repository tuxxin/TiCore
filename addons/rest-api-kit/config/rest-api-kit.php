<?php
// addons/rest-api-kit/config/rest-api-kit.php — read via config('rest-api-kit').
return [
    // Per-IP token-bucket rate limit on /api/v2 (mirrors qr-track's windowed limiter).
    'rate_limit'   => (int) (getenv('RESTAPIKIT_RATE_LIMIT') ?: 60),  // requests
    'rate_window'  => (int) (getenv('RESTAPIKIT_RATE_WINDOW') ?: 60), // seconds
    // Local IPs are exempt from throttling (matches the shared rate-limit pattern).
    'rate_exempt'  => ['127.0.0.1', '::1'],
    'rate_exempt_prefixes' => ['192.168.1.'],
    // Key shown to API clients in the admin prefix display (chars).
    'prefix_len'   => 10,
    // Server-held key used by the API console proxy so the key never reaches the
    // browser. Optional: set RESTAPIKIT_CONSOLE_KEY in .env to a full tk_... key
    // minted in the admin key manager.
    'console_key'  => getenv('RESTAPIKIT_CONSOLE_KEY') ?: '',
    // Base URL the proxy forwards to (defaults to this site).
    'api_base'     => defined('BASE_URL') ? BASE_URL : '',
];
