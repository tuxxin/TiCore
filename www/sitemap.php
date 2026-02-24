<?php
// www/sitemap.php

// 1. Manually define the absolute path to the TiCore directory
define('CORE_PATH', realpath(__DIR__ . '/../TiCore'));

// 2. Load the configuration (which now handles DotEnv and Autoloading)
require_once CORE_PATH . '/config.php';

// 3. Load the sitemap feeder logic
require_once CORE_PATH . '/feeds/sitemap.php';