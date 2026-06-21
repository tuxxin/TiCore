<?php
namespace Tuxxin\TiCore\Addons\GoogleAnalyticsGsc\Controllers;

use Tuxxin\TiCore\Addons\GoogleAnalyticsGsc\GoogleAuth;
use Tuxxin\TiCore\Addons\GoogleAnalyticsGsc\GscClient;
use Tuxxin\TiCore\Addons\GoogleAnalyticsGsc\Ga4Client;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

final class SeoStatsController
{
    private function cfg(): array { return config('google-analytics-gsc') ?: []; }

    /** GET /admin/seo-stats — show GSC + GA4 last-28-day totals. */
    public function index(Request $req): Response
    {
        $c   = $this->cfg();
        $sa  = (string) ($c['sa_json'] ?? '');
        $data = [
            'title'      => 'SEO Stats',
            'configured' => $sa !== '',
            'gsc_site'   => $c['gsc_site'] ?? '',
            'ga4'        => $c['ga4_property'] ?? '',
            'gsc'        => null,
            'gscError'   => null,
            'sessions'   => null,
            'ga4Error'   => null,
            'authError'  => null,
        ];

        if ($sa === '') {
            // Graceful "not configured".
            return Response::view('google-analytics-gsc::stats', $data);
        }

        $auth = new GoogleAuth($sa, (string) ($c['scopes'] ?? ''));
        if (!$auth->configured()) {
            $data['authError'] = $auth->lastError();
            return Response::view('google-analytics-gsc::stats', $data);
        }

        // Search Console totals.
        try {
            $gsc = new GscClient($auth, (string) ($c['gsc_site'] ?? ''));
            if ($gsc->configured()) {
                $data['gsc'] = $gsc->totals(28);
            } else {
                $data['gscError'] = 'GSC_SITE not set.';
            }
        } catch (\Throwable $e) {
            $data['gscError'] = $e->getMessage();
        }

        // GA4 totals.
        try {
            $ga4 = new Ga4Client($auth, (string) ($c['ga4_property'] ?? ''));
            if ($ga4->configured()) {
                $data['sessions'] = $ga4->totals(28);
            } else {
                $data['ga4Error'] = 'GA4_PROPERTY_ID not set.';
            }
        } catch (\Throwable $e) {
            $data['ga4Error'] = $e->getMessage();
        }

        return Response::view('google-analytics-gsc::stats', $data);
    }

    /** GET /admin/seo-stats/setup — the setup guide. */
    public function setup(): Response
    {
        $c = $this->cfg();
        return Response::view('google-analytics-gsc::setup-guide', [
            'title'        => 'SEO Stats — Setup',
            'sa_json'      => $c['sa_json'] ?? '',
            'ga4_property' => $c['ga4_property'] ?? '',
            'gsc_site'     => $c['gsc_site'] ?? '',
        ]);
    }
}
