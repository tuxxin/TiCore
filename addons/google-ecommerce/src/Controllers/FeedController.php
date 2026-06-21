<?php
namespace Tuxxin\TiCore\Addons\GoogleEcommerce\Controllers;

use Tuxxin\TiCore\Addons\GoogleEcommerce\MerchantFeed;
use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

final class FeedController
{
    private function cfg(): array { return config('google-ecommerce') ?: []; }

    /** GET /feeds/merchant.xml — render the Merchant Center product feed. */
    public function feed(Request $req): Response
    {
        $c   = $this->cfg();
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';

        $feed = new MerchantFeed(
            (string) ($c['title'] ?? 'Product feed'),
            $base,
            (string) ($c['description'] ?? 'Product feed'),
            (string) ($c['currency'] ?? 'USD')
        );

        $products = $c['products'] ?? [];
        if (is_callable($products)) {
            $products = $products();
        }
        $xml = $feed->build(is_iterable($products) ? $products : []);

        return Response::make($xml)
            ->withHeader('Content-Type', 'application/xml; charset=utf-8');
    }

    /** GET /admin/google-ecommerce — setup guide + link to the feed. */
    public function admin(): Response
    {
        $c = $this->cfg();
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $products = $c['products'] ?? [];
        if (is_callable($products)) {
            $products = $products();
        }
        return Response::view('google-ecommerce::setup-guide', [
            'title'       => 'Google Ecommerce',
            'feedUrl'     => $base . '/feeds/merchant.xml',
            'currency'    => $c['currency'] ?? 'USD',
            'feedSource'  => $c['feed_source'] ?? '',
            'productCount'=> is_countable($products) ? count($products) : 0,
        ]);
    }
}
