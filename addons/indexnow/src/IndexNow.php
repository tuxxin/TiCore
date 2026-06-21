<?php
namespace Tuxxin\TiCore\Addons\Indexnow;

/**
 * Minimal, dependency-free IndexNow REST client (no SDK). Submits a batch of
 * URLs to the IndexNow API so participating engines (Bing, Yandex, Seznam,
 * Naver…) re-crawl them promptly. Mirrors the addons/payments curl style.
 */
final class IndexNow
{
    public function __construct(
        private string $key,
        private string $host,
        private string $keyLocation,
        private string $endpoint = 'https://api.indexnow.org/indexnow'
    ) {}

    public function configured(): bool
    {
        return $this->key !== '' && $this->host !== '';
    }

    /**
     * Submit a list of absolute URLs. Returns ['code'=>int, 'body'=>string, 'count'=>int].
     * IndexNow treats 200/202 as success.
     */
    public function submit(array $urls): array
    {
        $urls = array_values(array_unique(array_filter(array_map('strval', $urls), fn($u) => $u !== '')));
        if (!$this->configured()) {
            throw new \RuntimeException('IndexNow is not configured — set INDEXNOW_KEY in .env.');
        }
        if (!$urls) {
            throw new \RuntimeException('No URLs to submit.');
        }

        $payload = [
            'host'        => $this->host,
            'key'         => $this->key,
            'keyLocation' => $this->keyLocation,
            'urlList'     => $urls,
        ];

        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8', 'Accept: application/json'],
            CURLOPT_TIMEOUT        => 25,
        ]);
        $res   = curl_exec($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno) {
            throw new \RuntimeException('IndexNow connection error: ' . $errno);
        }

        return ['code' => (int) $code, 'body' => (string) $res, 'count' => count($urls)];
    }

    /** Fetch an XML sitemap and extract the <loc> URLs from it. */
    public static function extractSitemapUrls(string $sitemapUrl): array
    {
        $ch = curl_init($sitemapUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_USERAGENT      => 'TiCore-IndexNow/1.0',
        ]);
        $xml   = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno || !is_string($xml) || $xml === '') {
            return [];
        }

        $urls = [];
        if (preg_match_all('#<loc>\s*([^<]+?)\s*</loc>#i', $xml, $m)) {
            foreach ($m[1] as $u) {
                $u = html_entity_decode(trim($u), ENT_QUOTES | ENT_XML1, 'UTF-8');
                if ($u !== '') $urls[] = $u;
            }
        }
        return array_values(array_unique($urls));
    }
}
