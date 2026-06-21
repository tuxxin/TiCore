<?php
// TiCore/src/Core/Middleware/AuthMiddleware.php
namespace TiCore\Core\Middleware;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

/**
 * Base session gate. The auth ADDON replaces/extends this with full user
 * accounts + roles; the base version just requires a logged-in session.
 */
final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $req, callable $next): Response
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $authed = !empty($_SESSION['user_id']) || !empty($_SESSION['logged_in']);
        if (!$authed) {
            if ($req->wantsJson()) return Response::json(['error' => 'Unauthenticated'], 401);
            $login = defined('LOGIN_URL') ? LOGIN_URL : '/login';
            return Response::redirect($login);
        }
        return $next($req);
    }
}
