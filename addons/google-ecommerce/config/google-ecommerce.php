<?php
// addons/google-ecommerce/config/google-ecommerce.php — read via config('google-ecommerce').
//
// `products` is a SAMPLE provider so /feeds/merchant.xml renders out of the box.
// Replace it with your real catalog: return an array of product rows, each with the
// keys consumed by MerchantFeed (id,title,description,link,image_link,availability,
// price,condition,brand?,gtin?,mpn?,google_product_category?). You can also swap this
// for a closure / DB query — MerchantFeed just iterates whatever array it is given.
return [
    'title'       => (defined('SITE_TITLE') ? SITE_TITLE : 'TiCore') . ' — Product feed',
    'description' => 'Google Merchant Center product feed',
    'currency'    => getenv('MERCHANT_CURRENCY') ?: 'USD',

    // feed_source note: where the live data should come from once you wire it up.
    'feed_source' => 'SAMPLE data from config. Replace config("google-ecommerce")["products"] '
                   . 'with your real catalog (array, closure, or DB query) before submitting to Merchant Center.',

    'products' => [
        [
            'id'                      => 'SAMPLE-001',
            'title'                   => 'Sample Widget — Blue',
            'description'             => 'A demonstration product so the Merchant feed renders out of the box. Replace me.',
            'link'                    => (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . '/products/sample-widget-blue',
            'image_link'              => (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . '/assets/images/logo-v2.png',
            'availability'            => 'in_stock',
            'price'                   => '19.99',
            'condition'               => 'new',
            'brand'                   => 'TiCore',
            'gtin'                    => '',
            'mpn'                     => 'SAMPLE-001',
            'google_product_category' => '',
        ],
        [
            'id'                      => 'SAMPLE-002',
            'title'                   => 'Sample Widget — Pro',
            'description'             => 'Second sample product. Demonstrates multiple <item> entries in the feed.',
            'link'                    => (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . '/products/sample-widget-pro',
            'image_link'              => (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . '/assets/images/logo-v2.png',
            'availability'            => 'in_stock',
            'price'                   => '49.00',
            'condition'               => 'new',
            'brand'                   => 'TiCore',
            'gtin'                    => '',
            'mpn'                     => 'SAMPLE-002',
            'google_product_category' => '',
        ],
    ],
];
