<?php
// TiCore/src/Controllers/HomeController.php
namespace TiCore\Controllers;

class HomeController {
    public function index(): void {
        $versionFile      = CORE_PATH . '/.v';
        $frameworkVersion = file_exists($versionFile)
            ? trim(file_get_contents($versionFile))
            : 'unknown';

        view('home', [
            'title'            => 'TiCore Framework',
            'meta_description' => 'TiCore is the only open-source PHP framework with a complete SEO suite built in — canonical, Open Graph, Twitter/X cards, JSON-LD, GA4, and more. Secure, lightweight, PHP 8.4+ native.',
            'og_type'          => 'website',
            'framework_version'=> $frameworkVersion,
            'app_env'          => getenv('APP_ENV') ?: 'production',
        ]);
    }
}
