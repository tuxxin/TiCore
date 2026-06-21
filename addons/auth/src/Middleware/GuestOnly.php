<?php
namespace Tuxxin\TiCore\Addons\Auth\Middleware;

use TiCore\Core\Middleware\MiddlewareInterface;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

/** Redirect already-authenticated users away from guest-only pages (login/register). */
final class GuestOnly implements MiddlewareInterface
{
    public function handle(Request $req, callable $next): Response
    {
        if (!empty($_SESSION['user_id'])) {
            return Response::redirect('/account');
        }
        return $next($req);
    }
}
