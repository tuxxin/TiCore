<?php
// addons/indexnow/config/indexnow.php — read via config('indexnow').
// INDEXNOW_KEY is a 32-char hex string the operator generates (e.g. bin2hex(random_bytes(16)))
// and sets in .env. It is served at /indexnow-key.txt and sent as the IndexNow `key`.
return [
    'key'      => getenv('INDEXNOW_KEY') ?: '',
    'endpoint' => 'https://api.indexnow.org/indexnow',
    // host + keyLocation are derived from BASE_URL at runtime.
    'host'         => defined('BASE_URL') ? parse_url(BASE_URL, PHP_URL_HOST) : '',
    'key_location' => defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/indexnow-key.txt' : '',
];
