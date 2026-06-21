<?php
namespace Tuxxin\TiCore\Addons\RestApiKit\Middleware;

use TiCore\Core\Middleware\MiddlewareInterface;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;
use Tuxxin\TiCore\Addons\RestApiKit\ApiKeys;

/**
 * Gate for the /api/v2 group. Mirrors qr-track's api.php:
 *   1. per-IP token-bucket rate limiting (429 JSON when exceeded; local IPs exempt)
 *   2. X-Api-Key header auth — look up by prefix, hash_equals the SHA-256 hash
 *      (401 JSON if missing/invalid), then stamp last_used_at.
 *
 * On success the verified key record is stashed on the Request params as
 * `_apikey` so downstream handlers (e.g. /whoami) can read it.
 */
final class ApiKeyAuth implements MiddlewareInterface
{
    public function handle(Request $req, callable $next): Response
    {
        $cfg = config('rest-api-kit') ?: [];
        $keys = new ApiKeys();

        // ── Rate limiting (skip exempt/local IPs) ──
        $ip = $req->ip();
        if (!$this->isExempt($ip, $cfg)) {
            $limit  = (int) ($cfg['rate_limit'] ?? 60);
            $window = (int) ($cfg['rate_window'] ?? 60);
            $rl = $keys->hitRateLimit($ip, $limit, $window);
            if (!$rl['allowed']) {
                return Response::json([
                    'error'       => 'Too Many Requests',
                    'limit'       => $rl['limit'],
                    'retry_after' => $rl['retry_after'],
                    'reset_at'    => $rl['reset_at'],
                ], 429)->withHeader('Retry-After', (string) $rl['retry_after']);
            }
        }

        // ── API key auth (header only — no query fallback) ──
        $presented = $req->header('X-Api-Key') ?? '';
        if (!is_string($presented) || $presented === '') {
            return Response::json(['error' => 'Unauthorized: missing X-Api-Key header'], 401);
        }
        $key = $keys->verify($presented);
        if ($key === null) {
            return Response::json(['error' => 'Unauthorized: invalid API key'], 401);
        }

        $keys->touch((int) $key['id']);
        $req->params['_apikey'] = $key;   // hand the verified record to the handler

        return $next($req);
    }

    private function isExempt(string $ip, array $cfg): bool
    {
        if ($ip === '') return false;
        foreach (($cfg['rate_exempt'] ?? []) as $exact) {
            if ($ip === $exact) return true;
        }
        foreach (($cfg['rate_exempt_prefixes'] ?? []) as $prefix) {
            if ($prefix !== '' && str_starts_with($ip, $prefix)) return true;
        }
        return false;
    }
}
