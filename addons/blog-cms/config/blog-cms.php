<?php
// addons/blog-cms/config/blog-cms.php — safe defaults (read via config('blog-cms')).
return [
    'site_name'  => 'TiCore Blog',
    'per_page'   => 10,
    // DB path may be overridden via the BLOGCMS_DB_PATH env var (resolved in the addon bootstrap).
    'db_path'    => getenv('BLOGCMS_DB_PATH') ?: (CORE_PATH . '/database/blog-cms.sqlite'),
];
