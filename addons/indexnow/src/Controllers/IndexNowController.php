<?php
namespace Tuxxin\TiCore\Addons\Indexnow\Controllers;

use Tuxxin\TiCore\Addons\Indexnow\IndexNow;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

final class IndexNowController
{
    private function cfg(): array { return config('indexnow') ?: []; }

    private function client(): IndexNow
    {
        $c = $this->cfg();
        return new IndexNow(
            $c['key'] ?? '',
            $c['host'] ?? '',
            $c['key_location'] ?? '',
            $c['endpoint'] ?? 'https://api.indexnow.org/indexnow'
        );
    }

    /** GET /indexnow-key.txt — serve the configured key as text/plain. */
    public function keyFile(): Response
    {
        $key = (string) ($this->cfg()['key'] ?? '');
        if ($key === '') {
            return Response::make('IndexNow key not configured', 404)
                ->withHeader('Content-Type', 'text/plain; charset=utf-8');
        }
        return Response::make($key)
            ->withHeader('Content-Type', 'text/plain; charset=utf-8');
    }

    /** GET /admin/indexnow — status + sitemap-submit form. */
    public function admin(): Response
    {
        $c = $this->cfg();
        return Response::view('indexnow::admin', [
            'title'       => 'IndexNow',
            'configured'  => ($c['key'] ?? '') !== '',
            'host'        => $c['host'] ?? '',
            'keyLocation' => $c['key_location'] ?? '',
            'sitemap'     => (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . '/sitemap.xml',
            'result'      => null,
            'error'       => null,
        ]);
    }

    /** POST /admin/indexnow/submit — fetch sitemap, submit its URLs. */
    public function submit(Request $req): Response
    {
        $c = $this->cfg();
        $sitemap = (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . '/sitemap.xml';
        $base = [
            'title'       => 'IndexNow',
            'configured'  => ($c['key'] ?? '') !== '',
            'host'        => $c['host'] ?? '',
            'keyLocation' => $c['key_location'] ?? '',
            'sitemap'     => $sitemap,
            'result'      => null,
            'error'       => null,
        ];

        $client = $this->client();
        if (!$client->configured()) {
            $base['error'] = 'IndexNow is not configured — set INDEXNOW_KEY in .env.';
            return Response::view('indexnow::admin', $base, 503);
        }

        $urls = IndexNow::extractSitemapUrls($sitemap);
        if (!$urls) {
            $base['error'] = 'No <loc> URLs found at ' . $sitemap . ' (sitemap missing or unreachable).';
            return Response::view('indexnow::admin', $base, 422);
        }

        try {
            $res = $client->submit($urls);
            \TiCore\Core\Logger::info('IndexNow submitted ' . $res['count'] . ' URL(s); HTTP ' . $res['code']);
            $base['result'] = $res;
        } catch (\Throwable $e) {
            $base['error'] = $e->getMessage();
            return Response::view('indexnow::admin', $base, 502);
        }
        return Response::view('indexnow::admin', $base);
    }
}
