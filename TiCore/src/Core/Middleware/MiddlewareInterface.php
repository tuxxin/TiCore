<?php
// TiCore/src/Core/Middleware/MiddlewareInterface.php
namespace TiCore\Core\Middleware;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

/**
 * One method, both before/after: run logic, then call $next($request) to
 * continue the onion, or return a Response early to short-circuit.
 */
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
