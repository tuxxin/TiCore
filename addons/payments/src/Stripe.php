<?php
namespace Tuxxin\TiCore\Addons\Payments;

/**
 * Minimal, dependency-free Stripe REST client (no SDK): Checkout Sessions,
 * session retrieval, and webhook signature verification over the HTTP API.
 */
final class Stripe
{
    public function __construct(private string $secret) {}

    public function configured(): bool { return $this->secret !== ''; }

    private function request(string $method, string $path, array $params = []): array
    {
        $ch = curl_init('https://api.stripe.com/v1/' . $path);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $this->secret, 'Stripe-Version: 2024-06-20'],
            CURLOPT_TIMEOUT        => 25,
        ];
        if ($method === 'POST') {
            $opts[CURLOPT_POST] = true;
            // Stripe accepts http_build_query's bracketed nesting (line_items[0][price]=…)
            $opts[CURLOPT_POSTFIELDS] = http_build_query($params);
        }
        curl_setopt_array($ch, $opts);
        $res  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($errno) throw new \RuntimeException('Stripe connection error: ' . $errno);
        $data = json_decode((string) $res, true) ?: [];
        if ($code >= 400) {
            throw new \RuntimeException('Stripe API error: ' . ($data['error']['message'] ?? ('HTTP ' . $code)));
        }
        return $data;
    }

    /** Create a hosted Checkout Session; returns the session (with ->url to redirect to). */
    public function createCheckoutSession(array $params): array
    {
        return $this->request('POST', 'checkout/sessions', $params);
    }

    public function retrieveSession(string $id): array
    {
        return $this->request('GET', 'checkout/sessions/' . rawurlencode($id));
    }

    /**
     * Verify a Stripe webhook signature (Stripe-Signature header). Returns the
     * decoded event array, or throws on invalid signature.
     */
    public static function verifyWebhook(string $payload, string $sigHeader, string $secret, int $tolerance = 300): array
    {
        $t = null; $v1 = [];
        foreach (explode(',', $sigHeader) as $part) {
            [$k, $v] = array_pad(explode('=', trim($part), 2), 2, '');
            if ($k === 't') $t = (int) $v;
            if ($k === 'v1') $v1[] = $v;
        }
        if (!$t || !$v1) throw new \RuntimeException('Malformed Stripe-Signature');
        $expected = hash_hmac('sha256', $t . '.' . $payload, $secret);
        $match = false;
        foreach ($v1 as $sig) { if (hash_equals($expected, $sig)) { $match = true; break; } }
        if (!$match) throw new \RuntimeException('Stripe signature mismatch');
        if (abs(time() - $t) > $tolerance) throw new \RuntimeException('Stripe timestamp outside tolerance');
        return json_decode($payload, true) ?: [];
    }
}
