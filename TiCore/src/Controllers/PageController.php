<?php
// TiCore/src/Controllers/PageController.php
namespace TiCore\Controllers;

class PageController {
    public function show($pageName) {
        // You can add global logic here (e.g., check login status)
        $title = ucwords(str_replace('-', ' ', $pageName));
        view($pageName, ['title' => $title]);
    }
}