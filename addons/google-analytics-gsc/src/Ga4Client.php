<?php
namespace Tuxxin\TiCore\Addons\GoogleAnalyticsGsc;

/**
 * Minimal GA4 Data API (analyticsdata v1beta) runReport client. Pulls
 * last-28-day session totals for a numeric property id.
 */
final class Ga4Client
{
    public function __construct(private GoogleAuth $auth, private string $propertyId) {}

    public function configured(): bool { return $this->auth->configured() && $this->propertyId !== ''; }

    /** Returns ['sessions'=>,'totalUsers'=>,'screenPageViews'=>] over the last $days days. */
    public function totals(int $days = 28): array
    {
        if (!$this->configured()) {
            throw new \RuntimeException('GA4 not configured (need SA JSON + GA4_PROPERTY_ID).');
        }
        $url = 'https://analyticsdata.googleapis.com/v1beta/properties/'
             . rawurlencode($this->propertyId) . ':runReport';

        $body = [
            'dateRanges' => [['startDate' => $days . 'daysAgo', 'endDate' => 'today']],
            'metrics'    => [
                ['name' => 'sessions'],
                ['name' => 'totalUsers'],
                ['name' => 'screenPageViews'],
            ],
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
            throw new \RuntimeException('GA4 connection error: ' . $errno);
        }
        $data = json_decode((string) $res, true) ?: [];
        if ($code >= 400) {
            throw new \RuntimeException('GA4 API error: ' . ($data['error']['message'] ?? ('HTTP ' . $code)));
        }

        $vals = $data['rows'][0]['metricValues'] ?? [];
        return [
            'sessions'        => (int) ($vals[0]['value'] ?? 0),
            'totalUsers'      => (int) ($vals[1]['value'] ?? 0),
            'screenPageViews' => (int) ($vals[2]['value'] ?? 0),
        ];
    }
}
