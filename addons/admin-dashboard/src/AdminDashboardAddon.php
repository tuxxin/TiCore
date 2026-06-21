<?php
namespace Tuxxin\TiCore\Addons\AdminDashboard;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class AdminDashboardAddon implements AddonInterface
{
    public function slug(): string { return 'admin-dashboard'; }

    public function register(AddonContext $ctx): void
    {
        $ctx->views('admin-dashboard');                                       // view('admin-dashboard::dashboard')
        $ctx->config('admin-dashboard', require $ctx->path . '/config/admin-dashboard.php');
        $ctx->routes('routes/web.php');                                       // 'auth' alias is provided by core
    }
}
