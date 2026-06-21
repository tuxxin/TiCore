<?php
namespace Tuxxin\TiCore\Addons\Payments\Controllers;

use Tuxxin\TiCore\Addons\Payments\Stripe;
use Tuxxin\TiCore\Addons\Payments\PayPal;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

final class CheckoutController
{
    private function cfg(): array { return config('payments') ?: []; }

    public function demo(): Response
    {
        return Response::view('payments::checkout', ['cfg' => $this->cfg(), 'title' => 'Payments demo']);
    }

    public function stripeCheckout(Request $req): Response
    {
        $c = $this->cfg();
        $stripe = new Stripe($c['stripe_secret'] ?? '');
        if (!$stripe->configured()) {
            return Response::make('Stripe is not configured — set STRIPE_SECRET_KEY in .env (use a TEST key).', 503);
        }
        try {
            $session = $stripe->createCheckoutSession([
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency'     => $c['currency'] ?? 'usd',
                        'product_data' => ['name' => 'TagTrack Pro — demo'],
                        'unit_amount'  => 199, // $1.99 (cents)
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => url('/pay/success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => url('/pay'),
                'metadata'    => ['source' => 'ticore-payments-demo'],
            ]);
            return Response::redirect($session['url']);
        } catch (\Throwable $e) {
            return Response::make('Payment error: ' . e($e->getMessage()), 502);
        }
    }

    public function success(Request $req): Response
    {
        $c = $this->cfg();
        $sid = (string) $req->query('session_id', '');
        $session = null;
        if ($sid !== '' && ($c['stripe_secret'] ?? '') !== '') {
            try { $session = (new Stripe($c['stripe_secret']))->retrieveSession($sid); } catch (\Throwable $e) {}
        }
        return Response::view('payments::success', ['cfg' => $c, 'session' => $session, 'title' => 'Thank you']);
    }

    public function stripeWebhook(Request $req): Response
    {
        $c = $this->cfg();
        $payload = file_get_contents('php://input') ?: '';
        $sig = $req->header('Stripe-Signature') ?? '';
        try {
            $event = Stripe::verifyWebhook($payload, $sig, $c['stripe_webhook'] ?? '');
        } catch (\Throwable $e) {
            return Response::make('Webhook signature verification failed', 400);
        }
        \TiCore\Core\Logger::info('Stripe webhook received: ' . ($event['type'] ?? 'unknown'));
        // Handle event types here, e.g. checkout.session.completed → fulfill order.
        return Response::json(['received' => true]);
    }

    public function paypalCreate(Request $req): Response
    {
        $c = $this->cfg();
        $pp = new PayPal($c['paypal_client_id'] ?? '', $c['paypal_secret'] ?? '', $c['paypal_env'] ?? 'sandbox');
        if (!$pp->configured()) return Response::json(['error' => 'PayPal not configured'], 503);
        try {
            $order = $pp->createOrder([['name' => 'TagTrack Pro — demo', 'amount' => 1.99, 'qty' => 1]], strtoupper($c['currency'] ?? 'usd'));
            return Response::json(['id' => $order['id'] ?? null]);
        } catch (\Throwable $e) {
            return Response::json(['error' => $e->getMessage()], 502);
        }
    }

    public function paypalCapture(Request $req): Response
    {
        $c = $this->cfg();
        $pp = new PayPal($c['paypal_client_id'] ?? '', $c['paypal_secret'] ?? '', $c['paypal_env'] ?? 'sandbox');
        $id = (string) $req->input('orderID', '');
        if ($id === '') return Response::json(['error' => 'missing orderID'], 400);
        try { return Response::json($pp->captureOrder($id)); }
        catch (\Throwable $e) { return Response::json(['error' => $e->getMessage()], 502); }
    }
}
