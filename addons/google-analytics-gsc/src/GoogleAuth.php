<?php
namespace Tuxxin\TiCore\Addons\GoogleAnalyticsGsc;

/**
 * Mints a short-lived Google OAuth2 access token from a service-account JSON,
 * dependency-free (no google/auth SDK). Pure PHP port of /home/tuxxin/.secrets/g_token.sh:
 * builds an RS256 JWT with openssl_sign() and exchanges it at the token endpoint
 * via the jwt-bearer grant. Tokens are cached in-process for their lifetime.
 */
final class GoogleAuth
{
    private array $sa = [];
    private string $error = '';
    /** @var array{token:string,exp:int}|null */
    private ?array $cache = null;

    public function __construct(private string $saJsonPath, private string $scopes)
    {
        if ($saJsonPath === '') {
            $this->error = 'No service-account JSON path configured (GOOGLE_APPLICATION_CREDENTIALS).';
            return;
        }
        if (!is_file($saJsonPath) || !is_readable($saJsonPath)) {
            $this->error = 'Service-account JSON not found/readable at: ' . $saJsonPath;
            return;
        }
        $json = json_decode((string) @file_get_contents($saJsonPath), true);
        if (!is_array($json) || empty($json['private_key']) || empty($json['client_email'])) {
            $this->error = 'Service-account JSON is malformed (missing private_key/client_email).';
            return;
        }
        $this->sa = $json;
    }

    public function configured(): bool { return $this->sa !== []; }
    public function clientEmail(): string { return $this->sa['client_email'] ?? ''; }
    public function lastError(): string { return $this->error; }

    /** base64url without padding, matching the shell b64url(). */
    private static function b64url(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    /** Return a valid access token, minting (and caching) one as needed. */
    public function accessToken(): string
    {
        if (!$this->configured()) {
            throw new \RuntimeException($this->error ?: 'Google auth not configured.');
        }
        if ($this->cache !== null && $this->cache['exp'] > time() + 60) {
            return $this->cache['token'];
        }

        $now = time();
        $exp = $now + 3600;
        $aud = $this->sa['token_uri'] ?? 'https://oauth2.googleapis.com/token';

        $header = self::b64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_UNESCAPED_SLASHES));
        $claim  = self::b64url(json_encode([
            'iss'   => $this->sa['client_email'],
            'scope' => $this->scopes,
            'aud'   => $aud,
            'iat'   => $now,
            'exp'   => $exp,
        ], JSON_UNESCAPED_SLASHES));

        $signingInput = $header . '.' . $claim;
        $signature = '';
        $ok = openssl_sign($signingInput, $signature, $this->sa['private_key'], OPENSSL_ALGO_SHA256);
        if (!$ok) {
            throw new \RuntimeException('Failed to RS256-sign the JWT (bad private_key?).');
        }
        $jwt = $signingInput . '.' . self::b64url($signature);

        $ch = curl_init($aud);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded', 'Accept: application/json'],
            CURLOPT_TIMEOUT        => 25,
        ]);
        $res   = curl_exec($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno) {
            throw new \RuntimeException('Google token connection error: ' . $errno);
        }
        $data = json_decode((string) $res, true) ?: [];
        if ($code >= 400 || empty($data['access_token'])) {
            throw new \RuntimeException('Google token error: ' . ($data['error_description'] ?? $data['error'] ?? ('HTTP ' . $code)));
        }

        $this->cache = ['token' => $data['access_token'], 'exp' => $exp];
        return $data['access_token'];
    }
}
