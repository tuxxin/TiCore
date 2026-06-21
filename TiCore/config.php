<?php
// TiCore/config.php — site configuration. Identity + secrets come from .env.

// 1. CORE_PATH fallback
if (!defined('CORE_PATH')) {
    define('CORE_PATH', __DIR__);
}

// 2-3. Load .env
require_once CORE_PATH . '/src/Core/DotEnv.php';
\TiCore\Core\DotEnv::load(CORE_PATH . '/.env');

// 4. Site identity (set these in .env)
define('SITE_TITLE',     getenv('SITE_TITLE') ?: 'TiCore Site');
define('BASE_URL',       rtrim(getenv('BASE_URL') ?: 'http://localhost', '/'));
define('SITE_LOGO',      getenv('SITE_LOGO') ?: '');
define('FACEBOOK_URL',   getenv('FACEBOOK_URL') ?: '');
define('GA4_ID',         getenv('GA4_ID') ?: '');
define('ADSENSE_CLIENT', getenv('ADSENSE_CLIENT') ?: '');
define('SITEMAP_ENABLED', true);

// 5. Database (set DB_ENABLED=true in .env to use MySQL/PDO)
define('DB_ENABLED', getenv('DB_ENABLED') === 'true');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: '');
define('DB_USER', getenv('DB_USER') ?: '');
define('DB_PASS', getenv('DB_PASS') ?: '');

// 6. Log level (0=CRITICAL … 5=DEBUG). Override via .env LOG_LEVEL.
$_logLevel = getenv('LOG_LEVEL');
if ($_logLevel !== false && is_numeric($_logLevel)) {
    define('LOG_LEVEL', (int) $_logLevel);
} elseif (getenv('APP_ENV') === 'development') {
    define('LOG_LEVEL', 3);
} else {
    define('LOG_LEVEL', 1);
}
unset($_logLevel);

// 7. Map log level -> error_reporting
$_masks = [
    0 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR,
    1 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR,
    2 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING | E_NOTICE | E_USER_NOTICE,
    3 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING | E_NOTICE | E_USER_NOTICE,
    4 => E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR | E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING | E_NOTICE | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED,
    5 => E_ALL,
];
error_reporting($_masks[min(LOG_LEVEL, 5)]);
unset($_masks);

// 8. Display errors — dev only
if (getenv('APP_ENV') === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// 9. Composer autoloader (optional — base ships without vendor/)
if (is_file(CORE_PATH . '/vendor/autoload.php')) {
    require_once CORE_PATH . '/vendor/autoload.php';
}
