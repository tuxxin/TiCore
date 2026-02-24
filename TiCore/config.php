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
define('SITE_TITLE', 'TiCore Secure Framework');
define('BASE_URL', 'https://ticore.tuxxin.com');
define('SITEMAP_ENABLED', false); // Toggle this to false to hide sitemap.xml

// 5. Database Settings
define('DB_ENABLED', false);
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));

// 6. Error Reporting (Keep development logs active)
if (getenv('APP_ENV') === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 7. Load Composer Autoloader
require_once CORE_PATH . '/vendor/autoload.php';