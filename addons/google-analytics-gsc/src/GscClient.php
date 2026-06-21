<?php
namespace Tuxxin\TiCore\Addons\GoogleAnalyticsGsc;

/**
 * Minimal Search Console (webmasters v3) Search Analytics client. Queries
 * last-28-day totals (clicks, impressions, ctr, position) for a property.
 */
final class GscClient
{
    public function __construct(private GoogleAuth $auth, private string $site) {}

    public function configured(): bool { return $this->auth->configured() && $this->site !== ''; }

    /** Returns ['clicks'=>,'impressions'=>,'ctr'=>,'position'=>] over the last $days days. */
    public function totals(int $days = 28): array
    {
        if (!$this->configured()) {
            throw new \RuntimeException('Search Console not configured (need SA JSON + GSC_SITE).');
        }
        // GSC data lags ~2-3 days; window ends 3 days ago to avoid empty rows.
        $end   = date('Y-m-d', strtotime('-3 days'));
        $start = date('Y-m-d', strtotime('-' . ($days + 3) . ' days'));

        $url = 'https://www.googleapis.com/webmasters/v3/sites/'
             . rawurlencode($this->site) . '/searchAnalytics/query';

        $body = [
            'startDate'  => $start,
            'endDate'    => $end,
            'dimensions' => [],          // no dimensions => one aggregate totals row
            'rowLimit'   => 1,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body, JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->auth->accessToken(),
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 25,
        ]);
        $res   = curl_exec($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno) {
            throw new \RuntimeException('GSC connection error: ' . $errno);
        }
        $data = json_decode((string) $res, true) ?: [];
        if ($code >= 400) {
            throw new \RuntimeException('GSC API error: ' . ($data['error']['message'] ?? ('HTTP ' . $code)));
        }

        $row = $data['rows'][0] ?? [];
        return [
            'start'       => $start,
            'end'         => $end,
            'clicks'      => (int) ($row['clicks'] ?? 0),
            'impressions' => (int) ($row['impressions'] ?? 0),
            'ctr'         => (float) ($row['ctr'] ?? 0),
            'position'    => (float) ($row['position'] ?? 0),
        ];
    }
}
