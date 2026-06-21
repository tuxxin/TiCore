<?php
namespace Tuxxin\TiCore\Addons\RestApiKit;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class RestApiKitAddon implements AddonInterface
{
    public function slug(): string { return 'rest-api-kit'; }

    public function register(AddonContext $ctx): void
    {
        if (!defined('RESTAPIKIT_DB_PATH')) {
            define('RESTAPIKIT_DB_PATH', getenv('RESTAPIKIT_DB_PATH') ?: (CORE_PATH . '/database/rest-api-kit.sqlite'));
        }
        $ctx->views('rest-api-kit');                                          // view('rest-api-kit::keys') etc.
        $ctx->config('rest-api-kit', require $ctx->path . '/config/rest-api-kit.php');
        $ctx->middleware('apikey', Middleware\ApiKeyAuth::class);             // 'auth'/'csrf' aliases come from core
        $ctx->routes('routes/web.php');
        $ctx->routes('routes/api.php');
    }
}
