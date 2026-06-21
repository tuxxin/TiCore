<?php
namespace Tuxxin\TiCore\Addons\RestApiKit\Controllers;

use Tuxxin\TiCore\Addons\RestApiKit\ApiKeys;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;
use TiCore\Core\Security;

/**
 * Auth-protected admin: manage API keys + a live API console whose requests are
 * proxied server-side (the API key never reaches the browser — mirrors
 * qr-track's api_console_proxy.php).
 */
final class ApiAdminController
{
    private function keys(): ApiKeys { return new ApiKeys(); }
    private function cfg(): array { return config('rest-api-kit') ?: []; }

    // ── Key manager ────────────────────────────────────────────────────────────

    public function keysIndex(Request $req): Response
    {
        return Response::view('rest-api-kit::keys', [
            'title'   => 'API Keys',
            'keys'    => $this->keys()->all(),
            'created' => null,   // full plaintext (shown once) after a create
            'error'   => null,
        ]);
    }

    public function keysCreate(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $name = trim((string) $req->input('name', ''));
        $minted = $this->keys()->create($name);

        return Response::view('rest-api-kit::keys', [
            'title'   => 'API Keys',
            'keys'    => $this->keys()->all(),
            'created' => $minted['plaintext'],   // show ONCE
            'error'   => null,
        ]);
    }

    public function keysRevoke(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $id = (int) $req->param('id', 0);
        if ($id > 0) $this->keys()->revoke($id);
        return Response::redirect('/admin/api-keys');
    }

    // ── Live console ─────────────────────────────────────────────────────────────

    public function console(Request $req): Response
    {
        $cfg = $this->cfg();
        return Response::view('rest-api-kit::console', [
            'title'        => 'API Console',
            'has_key'      => ($cfg['console_key'] ?? '') !== '',
            'api_base'     => $cfg['api_base'] ?? '',
            'result'       => null,
            'status'       => null,
            'sent_method'  => 'GET',
            'sent_path'    => '/api/v2/ping',
        ]);
    }

    /**
     * Server-side proxy: forward a chosen method+path to this site's own /api/v2
     * endpoint WITH the server-held key, so the key never reaches the browser.
     * Mirrors qr-track's api_console_proxy.php (auth + CSRF already enforced by
     * the route middleware / this check).
     */
    public function consoleProxy(Request $req): Response
    {
        if (!Security::csrfValid((string) $req->input('csrf_token'))) {
            return Response::make('Invalid CSRF token', 419);
        }
        $cfg = $this->cfg();

        $method = strtoupper((string) $req->input('method', 'GET'));
        if (!in_array($method, ['GET', 'POST'], true)) $method = 'GET';

        // Constrain to the addon's own /api/v2 surface (no SSRF to arbitrary URLs).
        $path = '/' . ltrim((string) $req->input('path', '/api/v2/ping'), '/');
        if (!str_starts_with($path, '/api/v2/')) {
            $path = '/api/v2/ping';
        }
        $payload = (string) $req->input('payload', '');

        $base = rtrim((string) ($cfg['api_base'] ?? (defined('BASE_URL') ? BASE_URL : '')), '/');
        $serverKey = (string) ($cfg['console_key'] ?? '');

        if ($serverKey === '') {
            return Response::view('rest-api-kit::console', [
                'title'        => 'API Console',
                'has_key'      => false,
                'api_base'     => $base,
                'result'       => "Server-held console key is not configured.\nMint a key in the manager above, then set RESTAPIKIT_CONSOLE_KEY in .env.",
                'status'       => 503,
                'sent_method'  => $method,
                'sent_path'    => $path,
            ]);
        }

        $url = $base . $path;
        $ch = curl_init($url);
        $headers = ['X-Api-Key: ' . $serverKey, 'Accept: application/json'];
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_HTTPHEADER     => $headers,
        ];
        if ($method === 'POST') {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = $payload;
            $headers[] = 'Content-Type: application/json';
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }
        curl_setopt_array($ch, $opts);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);

        $result = $errno
            ? ('Proxy connection error: ' . $errno)
            : ($body === false ? 'No response from API' : (string) $body);

        return Response::view('rest-api-kit::console', [
            'title'        => 'API Console',
            'has_key'      => true,
            'api_base'     => $base,
            'result'       => $result,
            'status'       => $errno ? 502 : $code,
            'sent_method'  => $method,
            'sent_path'    => $path,
        ]);
    }
}
