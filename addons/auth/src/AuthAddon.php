<?php
namespace Tuxxin\TiCore\Addons\Auth;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class AuthAddon implements AddonInterface
{
    public function slug(): string { return 'auth'; }

    public function register(AddonContext $ctx): void
    {
        if (!defined('AUTH_DB_PATH')) {
            define('AUTH_DB_PATH', getenv('AUTH_DB_PATH') ?: (CORE_PATH . '/database/auth.sqlite'));
        }
        $ctx->views('auth');                                   // view('auth::login') etc.
        $ctx->config('auth', require $ctx->path . '/config/auth.php');
        $ctx->middleware('guest', Middleware\GuestOnly::class); // 'auth' alias is provided by core
        $ctx->routes('routes/web.php');
    }
}
