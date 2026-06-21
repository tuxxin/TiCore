<?php
namespace Tuxxin\TiCore\Addons\GoogleEcommerce;

/**
 * Builds a Google Merchant Center product feed: RSS 2.0 with the
 * http://base.google.com/ns/1.0 (g:) namespace. Required per-item fields:
 * g:id, g:title, g:description, g:link, g:image_link, g:availability,
 * g:price, g:condition. Optional fields (brand, gtin, mpn,
 * google_product_category) are emitted when present.
 */
final class MerchantFeed
{
    public function __construct(
        private string $channelTitle,
        private string $channelLink,
        private string $channelDescription,
        private string $currency = 'USD'
    ) {}

    /** Wrap a scalar in a CDATA section after stripping anything illegal in XML CDATA. */
    private static function cdata(string $v): string
    {
        $v = str_replace(']]>', ']]]]><![CDATA[>', $v);
        // strip control chars not permitted in XML 1.0
        $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $v) ?? '';
        return '<![CDATA[' . $v . ']]>';
    }

    /** Format a price as "<amount> <CURRENCY>" if a bare number was supplied. */
    private function price(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') return '';
        // Already includes a currency code? leave it.
        if (preg_match('/[A-Za-z]{3}\s*$/', $raw) || preg_match('/^[A-Za-z]{3}\s/', $raw)) {
            return $raw;
        }
        return number_format((float) $raw, 2, '.', '') . ' ' . $this->currency;
    }

    /** @param iterable<array<string,mixed>> $products */
    public function build(iterable $products): string
    {
        $now = date(DATE_RSS);
        $out  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $out .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $out .= "  <channel>\n";
        $out .= '    <title>' . self::cdata($this->channelTitle) . "</title>\n";
        $out .= '    <link>' . htmlspecialchars($this->channelLink, ENT_QUOTES | ENT_XML1, 'UTF-8') . "</link>\n";
        $out .= '    <description>' . self::cdata($this->channelDescription) . "</description>\n";
        $out .= '    <lastBuildDate>' . $now . "</lastBuildDate>\n";

        foreach ($products as $p) {
            $out .= $this->item((array) $p);
        }

        $out .= "  </channel>\n";
        $out .= "</rss>\n";
        return $out;
    }

    private function item(array $p): string
    {
        $get = static fn(string $k): string => isset($p[$k]) ? trim((string) $p[$k]) : '';

        $required = [
            'g:id'           => $get('id'),
            'g:title'        => $get('title'),
            'g:description'  => $get('description'),
            'g:link'         => $get('link'),
            'g:image_link'   => $get('image_link'),
            'g:availability' => $get('availability') ?: 'in_stock',
            'g:price'        => $this->price($get('price')),
            'g:condition'    => $get('condition') ?: 'new',
        ];

        $optional = array_filter([
            'g:brand'                   => $get('brand'),
            'g:gtin'                    => $get('gtin'),
            'g:mpn'                     => $get('mpn'),
            'g:google_product_category' => $get('google_product_category'),
        ], static fn($v) => $v !== '');

        $line = "    <item>\n";
        foreach (array_merge($required, $optional) as $tag => $val) {
            // URLs use entity-escaping; text uses CDATA.
            if (in_array($tag, ['g:link', 'g:image_link'], true)) {
                $line .= '      <' . $tag . '>' . htmlspecialchars($val, ENT_QUOTES | ENT_XML1, 'UTF-8') . '</' . $tag . ">\n";
            } else {
                $line .= '      <' . $tag . '>' . self::cdata($val) . '</' . $tag . ">\n";
            }
        }
        $line .= "    </item>\n";
        return $line;
    }
}
