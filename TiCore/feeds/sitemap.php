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

// Manual Entries
$manualEntries = [
    ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
];

$dynamicRoutes = [];

// Auto-scan Controllers (e.g., HomeController.php -> /home)
$controllers = glob(CORE_PATH . '/src/Controllers/*Controller.php');
if ($controllers) {
    foreach ($controllers as $file) {
        $name = basename($file, 'Controller.php');
        if (in_array($name, ['Page', 'Home'])) continue;
        $uri = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));
        $dynamicRoutes[] = ['loc' => '/' . $uri, 'priority' => '0.8', 'changefreq' => 'weekly'];
    }
}

// Auto-scan Fallback Templates (e.g., about.php -> /about)
$views = glob(CORE_PATH . '/templates/default/*.php');
if ($views) {
    foreach ($views as $file) {
        $name = basename($file, '.php');
        if (in_array($name, ['404', 'home', 'header', 'footer'])) continue;
        
        // Don't add if already added via Controller
        $exists = false;
        foreach ($dynamicRoutes as $route) {
            if ($route['loc'] === '/' . $name) $exists = true;
        }
        
        if (!$exists) {
            $dynamicRoutes[] = ['loc' => '/' . $name, 'priority' => '0.6', 'changefreq' => 'monthly'];
        }
    }
}

$allUrls = array_merge($manualEntries, $dynamicRoutes);

// XML Output
header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($allUrls as $url): ?>
    <url>
        <loc><?= htmlspecialchars(BASE_URL . $url['loc']) ?></loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
        <changefreq><?= $url['changefreq'] ?></changefreq>
        <priority><?= $url['priority'] ?></priority>
    </url>
    <?php endforeach; ?>
</urlset>