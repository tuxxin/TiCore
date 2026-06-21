<?php
namespace Tuxxin\TiCore\Addons\Payments;

/** Minimal, dependency-free PayPal Orders v2 REST client (sandbox or live). */
final class PayPal
{
    private string $base;

    public function __construct(private string $clientId, private string $secret, string $env = 'sandbox')
    {
        $this->base = ($env === 'live')
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function configured(): bool { return $this->clientId !== '' && $this->secret !== ''; }

    private function token(): string
    {
        $ch = curl_init($this->base . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $this->clientId . ':' . $this->secret,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_TIMEOUT        => 25,
        ]);
        $res = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        $data = json_decode((string) $res, true) ?: [];
        if ($code >= 400 || empty($data['access_token'])) {
            throw new \RuntimeException('PayPal auth failed: ' . ($data['error_description'] ?? ('HTTP ' . $code)));
        }
        return $data['access_token'];
    }

    private function api(string $method, string $path, ?array $body = null): array
    {
        $ch = curl_init($this->base . $path);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $this->token(), 'Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 25,
        ];
        if ($body !== null) $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        curl_setopt_array($ch, $opts);
        $res = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        $data = json_decode((string) $res, true) ?: [];
        if ($code >= 400) throw new \RuntimeException('PayPal API error: ' . ($data['message'] ?? ('HTTP ' . $code)));
        return $data;
    }

    /** Create an order. $items: [['name'=>,'amount'=>float,'qty'=>int], ...] */
    public function createOrder(array $items, string $currency = 'USD'): array
    {
        $total = 0.0;
        $breakdown = [];
        foreach ($items as $it) {
            $amt = round((float) $it['amount'], 2);
            $qty = max(1, (int) ($it['qty'] ?? 1));
            $total += $amt * $qty;
            $breakdown[] = [
                'name'       => substr((string) $it['name'], 0, 127),
                'quantity'   => (string) $qty,
                'unit_amount'=> ['currency_code' => $currency, 'value' => number_format($amt, 2, '.', '')],
            ];
        }
        $value = number_format($total, 2, '.', '');
        return $this->api('POST', '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $currency,
                    'value'         => $value,
                    'breakdown'     => ['item_total' => ['currency_code' => $currency, 'value' => $value]],
                ],
                'items' => $breakdown,
            ]],
        ]);
    }

    public function captureOrder(string $orderId): array
    {
        return $this->api('POST', '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture', []);
    }
}
