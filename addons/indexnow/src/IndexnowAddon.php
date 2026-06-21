<?php
namespace Tuxxin\TiCore\Addons\Indexnow;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class IndexnowAddon implements AddonInterface
{
    public function slug(): string { return 'indexnow'; }

    public function register(AddonContext $ctx): void
    {
        $ctx->views('indexnow');
        $ctx->config('indexnow', require $ctx->path . '/config/indexnow.php');
        $ctx->routes('routes/web.php');
    }
}
