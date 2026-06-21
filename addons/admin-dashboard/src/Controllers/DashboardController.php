<?php
namespace Tuxxin\TiCore\Addons\AdminDashboard\Controllers;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

final class DashboardController
{
    public function index(Request $req): Response
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email = $_SESSION['user_email'] ?? null;
        $role  = $_SESSION['user_role'] ?? null;

        return Response::view('admin-dashboard::dashboard', [
            'title'   => 'Admin Dashboard',
            'cfg'     => config('admin-dashboard') ?: [],
            'email'   => $email,
            'role'    => $role,
            'php'     => PHP_VERSION,
            'addons'  => $this->installedAddons(),
        ]);
    }

    /**
     * Scan CORE_PATH/addons/<slug>/addon.json and return slug+version+description
     * for each installed addon. Mirrors AddonManager discovery (no recursion).
     *
     * @return array<int,array{slug:string,name:string,version:string,description:string}>
     */
    private function installedAddons(): array
    {
        $out = [];
        $dir = CORE_PATH . '/addons';
        foreach (glob($dir . '/*/addon.json') ?: [] as $mf) {
            $m = json_decode((string) @file_get_contents($mf), true);
            if (!is_array($m) || empty($m['slug'])) continue;
            $out[] = [
                'slug'        => (string) $m['slug'],
                'name'        => (string) ($m['name'] ?? $m['slug']),
                'version'     => (string) ($m['version'] ?? '—'),
                'description' => (string) ($m['description'] ?? ''),
            ];
        }
        usort($out, fn($a, $b) => strcmp($a['slug'], $b['slug']));
        return $out;
    }
}
