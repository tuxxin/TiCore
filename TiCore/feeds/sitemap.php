<?php
// TiCore/feeds/sitemap.php

// Trigger 404 if disabled
if (!defined('SITEMAP_ENABLED') || SITEMAP_ENABLED !== true) {
    header("HTTP/1.0 404 Not Found");
    $errorView = CORE_PATH . '/templates/default/404.php';
    if (file_exists($errorView)) {
        require $errorView;
    } else {
        echo "404 Not Found";
    }
    exit;
}

$tplBase = CORE_PATH . '/templates/default';

// Helper: latest mtime from one or more template files
$mtime = static function (string ...$files) use ($tplBase): string {
    $latest = 0;
    foreach ($files as $f) {
        $path = $tplBase . '/' . $f;
        if (file_exists($path)) {
            $t = filemtime($path);
            if ($t > $latest) $latest = $t;
        }
    }
    return $latest ? date('Y-m-d', $latest) : date('Y-m-d');
};

// Manual Entries
$manualEntries = [
    ['loc' => '/', 'lastmod' => $mtime('home.php'), 'priority' => '1.0', 'changefreq' => 'daily'],
];

$dynamicRoutes = [];
$seen = ['/', 'home'];

// Auto-scan Controllers (e.g., HomeController.php -> /home)
$controllers = glob(CORE_PATH . '/src/Controllers/*Controller.php') ?: [];
foreach ($controllers as $file) {
    $name = basename($file, 'Controller.php');
    if (in_array($name, ['Page', 'Home'])) continue;
    $uri = '/' . strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));
    if (in_array($uri, $seen)) continue;
    $seen[] = $uri;
    $tplFile = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name)) . '.php';
    $dynamicRoutes[] = [
        'loc'        => $uri,
        'lastmod'    => $mtime($tplFile),
        'priority'   => '0.8',
        'changefreq' => 'weekly',
    ];
}

// Auto-scan flat templates (e.g., about.php -> /about)
$flatViews = glob($tplBase . '/*.php') ?: [];
foreach ($flatViews as $file) {
    $name = basename($file, '.php');
    if (in_array($name, ['404', 'home', 'header', 'footer'])) continue;
    $uri = '/' . $name;
    if (in_array($uri, $seen)) continue;
    $seen[] = $uri;
    $dynamicRoutes[] = [
        'loc'        => $uri,
        'lastmod'    => $mtime($name . '.php'),
        'priority'   => '0.6',
        'changefreq' => 'monthly',
    ];
}

// Auto-scan nested templates (e.g., docs/cli.php -> /docs/cli)
$subdirs = glob($tplBase . '/*', GLOB_ONLYDIR) ?: [];
foreach ($subdirs as $dir) {
    $parentName = basename($dir);
    if (in_array($parentName, ['layouts'])) continue; // skip layout partials
    $nestedViews = glob($dir . '/*.php') ?: [];
    foreach ($nestedViews as $file) {
        $name = basename($file, '.php');
        if ($name[0] === '_') continue; // skip _sidebar.php etc.
        $uri = '/' . $parentName . '/' . $name;
        if (in_array($uri, $seen)) continue;
        $seen[] = $uri;
        $dynamicRoutes[] = [
            'loc'        => $uri,
            'lastmod'    => $mtime($parentName . '/' . $name . '.php'),
            'priority'   => '0.7',
            'changefreq' => 'monthly',
        ];
    }
}

$allUrls = array_merge($manualEntries, $dynamicRoutes);

// XML Output
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
echo '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
echo '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";
foreach ($allUrls as $url): ?>
    <url>
        <loc><?= htmlspecialchars(BASE_URL . $url['loc']) ?></loc>
        <lastmod><?= htmlspecialchars($url['lastmod']) ?></lastmod>
        <changefreq><?= $url['changefreq'] ?></changefreq>
        <priority><?= $url['priority'] ?></priority>
    </url>
<?php endforeach;
echo '</urlset>';
