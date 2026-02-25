<?php
// TiCore/config.php

// 1. Define CORE_PATH if not already set (fallback for direct access)
if (!defined('CORE_PATH')) {
    define('CORE_PATH', __DIR__);
}

// 2. Manually require DotEnv to ensure it's available for the next step
require_once CORE_PATH . '/src/Core/DotEnv.php';

// 3. Load environment variables
\TiCore\Core\DotEnv::load(CORE_PATH . '/.env');

// 4. General Settings
define('SITE_TITLE',    'TiCore Secure Framework');
define('BASE_URL',      'https://ticore.tuxxin.com');
define('SITE_LOGO',     BASE_URL . '/assets/images/logo-v2.png');
define('FACEBOOK_URL',  'https://facebook.com/tuxxin');
define('GA4_ID',        'G-9RF6FP6ZGX');
define('SITEMAP_ENABLED', false); // Toggle this to false to hide sitemap.xml

// 5. Database Settings
define('DB_ENABLED', false);
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));

// 6. Log Level (0–5)
//    0 = CRITICAL  — fatal errors and parse failures only
//    1 = ERROR     — runtime errors (+ critical)
//    2 = WARNING   — warnings, notices (+ error + critical)
//    3 = INFO      — informational messages (+ warning + error + critical)
//    4 = DEPRECATED — deprecated / user-deprecated (+ above)
//    5 = DEBUG     — E_ALL, strict mode (+ above)
//
//    Can be overridden via .env: LOG_LEVEL=3
$_logLevel = getenv('LOG_LEVEL');
if ($_logLevel !== false && is_numeric($_logLevel)) {
    define('LOG_LEVEL', (int)$_logLevel);
} elseif (getenv('APP_ENV') === 'development') {
    define('LOG_LEVEL', 3); // INFO in development
} else {
    define('LOG_LEVEL', 1); // ERROR in production
}
unset($_logLevel);

// 7. Map log level to PHP error_reporting bitmask
$_errorMasks = [
    0 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR,
    1 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR
        | E_USER_ERROR,
    2 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR
        | E_USER_ERROR | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING
        | E_USER_WARNING | E_NOTICE | E_USER_NOTICE,
    3 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR
        | E_USER_ERROR | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING
        | E_USER_WARNING | E_NOTICE | E_USER_NOTICE,
    4 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR
        | E_USER_ERROR | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING
        | E_USER_WARNING | E_NOTICE | E_USER_NOTICE
        | E_DEPRECATED | E_USER_DEPRECATED,
    5 => E_ALL,
];
error_reporting($_errorMasks[min(LOG_LEVEL, 5)]);
unset($_errorMasks);

// 8. Display errors — on in development, off in production
if (getenv('APP_ENV') === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// 9. Load Composer Autoloader
require_once CORE_PATH . '/vendor/autoload.php';
