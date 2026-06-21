<?php
// TiCore/src/functions_global.php

$GLOBALS['__view_namespaces'] = $GLOBALS['__view_namespaces'] ?? [];

/** Register an addon/view namespace: view('auth::login') -> $dir/login.php */
function view_namespace(string $ns, string $dir): void {
    $GLOBALS['__view_namespaces'][$ns] = rtrim($dir, '/');
}

function view($viewName, $data = []) {
    // Extract array keys as variables (EXTR_SKIP avoids clobbering existing vars)
    extract($data, EXTR_SKIP);

    if (str_contains($viewName, '::')) {
        // Namespaced view (addon): "slug::name"
        [$ns, $name] = explode('::', $viewName, 2);
        $dir  = $GLOBALS['__view_namespaces'][$ns] ?? null;
        $path = $dir ? $dir . '/' . $name . '.php' : null;
    } else {
        $path = CORE_PATH . '/templates/default/' . $viewName . '.php';
    }

    if ($path && is_file($path)) {
        require $path;
    } else {
        echo 'View not found: ' . htmlspecialchars($viewName, ENT_QUOTES, 'UTF-8');
    }
}

// Shortcode for Security::e() (Sanitize Output)
function e($string) {
    return \TiCore\Core\Security::e($string);
}

// Shortcode for CSRF Field in forms
function csrf_field() {
    $token = \TiCore\Core\Security::generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/** Absolute URL from a site-relative path (+ optional query). */
function url(string $path = '', array $query = []): string {
    $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
    $u = $base . '/' . ltrim($path, '/');
    return $query ? $u . '?' . http_build_query($query) : $u;
}

/** Absolute URL for a named route: route('blog.show', ['id'=>5]). */
function route(string $name, array $params = []): string {
    $r = \TiCore\Core\Router::instance();
    return $r ? $r->routeUrl($name, $params) : url('');
}

/** Issue a redirect and stop. Accepts a path or absolute URL. */
function redirect(string $to, int $status = 302): void {
    if (!preg_match('#^https?://#i', $to)) {
        $to = url($to);
    }
    header('Location: ' . $to, true, $status);
    exit;
}

// ── Simple config registry (addons merge defaults here; sites read via config()) ──
$GLOBALS['__config'] = $GLOBALS['__config'] ?? [];
function config_set(string $key, $value): void { $GLOBALS['__config'][$key] = $value; }
function config(string $key, $default = null) { return $GLOBALS['__config'][$key] ?? $default; }
