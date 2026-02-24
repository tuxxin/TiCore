<?php
// TiCore/src/functions_global.php

function view($viewName, $data = []) {
    // Extract array keys as variables (e.g. ['title'=>'Hi'] becomes $title)
    extract($data);
    
    $path = CORE_PATH . '/templates/default/' . $viewName . '.php';
    
    if (file_exists($path)) {
        require $path;
    } else {
        echo "View file not found: $viewName";
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