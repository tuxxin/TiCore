<?php
namespace Tuxxin\TiCore\Addons\GoogleAnalyticsGsc;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class GoogleAnalyticsGscAddon implements AddonInterface
{
    public function slug(): string { return 'google-analytics-gsc'; }

    public function register(AddonContext $ctx): void
    {
        $ctx->views('google-analytics-gsc');
        $ctx->config('google-analytics-gsc', require $ctx->path . '/config/google-analytics-gsc.php');
        $ctx->routes('routes/web.php');
    }
}
