<?php
// TiCore/src/Core/Middleware/Pipeline.php
namespace TiCore\Core\Middleware;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

/** Dependency-free onion runner for the middleware stack. */
final class Pipeline
{
    /**
     * @param MiddlewareInterface[] $middleware
     * @param callable $destination fn(Request): Response|string|null  (the route handler)
     */
    public static function run(Request $req, array $middleware, callable $destination): Response
    {
        $core = static function (Request $r) use ($destination): Response {
            return self::toResponse($destination($r));
        };

        $stack = array_reduce(
            array_reverse($middleware),
            static function (callable $next, MiddlewareInterface $mw): callable {
                return static function (Request $r) use ($mw, $next): Response {
                    return $mw->handle($r, $next);
                };
            },
            $core
        );

        return $stack($req);
    }

    public static function toResponse($result): Response
    {
        if ($result instanceof Response) return $result;
        if (is_string($result)) return Response::make($result);
        return Response::make(''); // null/void — handler echoed (Router captured it)
    }
}
