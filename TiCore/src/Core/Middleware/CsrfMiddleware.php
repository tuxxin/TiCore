<?php
// TiCore/src/Core/Middleware/CsrfMiddleware.php
namespace TiCore\Core\Middleware;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;
use TiCore\Core\Security;

/** Verifies a CSRF token on state-changing methods. Apply via ->middleware('csrf'). */
final class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $req, callable $next): Response
    {
        if (in_array($req->method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $req->input('csrf_token') ?? $req->header('X-CSRF-Token') ?? '';
            if (!Security::csrfValid(is_string($token) ? $token : '')) {
                return Response::make('Invalid CSRF token', 419);
            }
        }
        return $next($req);
    }
}
