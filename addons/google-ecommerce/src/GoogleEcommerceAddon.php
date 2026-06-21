<?php
namespace Tuxxin\TiCore\Addons\GoogleEcommerce;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class GoogleEcommerceAddon implements AddonInterface
{
    public function slug(): string { return 'google-ecommerce'; }

    public function register(AddonContext $ctx): void
    {
        $ctx->views('google-ecommerce');
        $ctx->config('google-ecommerce', require $ctx->path . '/config/google-ecommerce.php');
        $ctx->routes('routes/web.php');
    }
}
