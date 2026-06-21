<?php
// TiCore/src/Core/Seo/SchemaBuilder.php
namespace TiCore\Core\Seo;

/**
 * Config-driven schema.org JSON-LD builder (the structured-data "classifier").
 * Emits a WebSite + Organization + WebPage @graph from page context, enriched by
 * optional config constants. Per-page nodes (Product/Article/FAQ/Breadcrumb/…)
 * are appended via $ctx['extra']. Neutral defaults — no site-specific identity
 * unless the consuming site defines the constants.
 *
 * Config constants (all optional): SITE_ORG_NAME, SITE_ORG_URL, SITE_ORG_TYPE,
 * SITE_PAGE_TYPE, SITE_SAMEAS (array|csv), SITE_CATEGORY, SITE_CONTACT_EMAIL,
 * SITE_CONTACT_PHONE, SITE_CONTACT_TYPE, SITE_ADDRESS (array), SITE_RATING,
 * SITE_RATING_COUNT. Use the schema-generator addon to produce these.
 */
final class SchemaBuilder
{
    public static function script(array $ctx): string
    {
        return json_encode(
            ['@context' => 'https://schema.org', '@graph' => self::graph($ctx)],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }

    public static function graph(array $ctx): array
    {
        $base  = rtrim((string) ($ctx['baseUrl'] ?? ''), '/');
        $site  = (string) ($ctx['siteName'] ?? '');
        $canon = (string) ($ctx['canonical'] ?? $base);
        $desc  = (string) ($ctx['description'] ?? '');

        $orgUrl = rtrim(self::c('SITE_ORG_URL', $base), '/');
        $orgId  = $orgUrl . '/#organization';
        $org = [
            '@type' => self::c('SITE_ORG_TYPE', 'Organization'),
            '@id'   => $orgId,
            'name'  => self::c('SITE_ORG_NAME', $site),
            'url'   => $orgUrl,
        ];
        if (!empty($ctx['logo'])) {
            $org['logo'] = ['@type' => 'ImageObject', 'url' => (string) $ctx['logo']];
        }
        $sameAs = self::arr('SITE_SAMEAS');
        if (!empty($ctx['facebook'])) $sameAs[] = (string) $ctx['facebook'];
        $sameAs = array_values(array_unique(array_filter($sameAs)));
        if ($sameAs) $org['sameAs'] = $sameAs;
        if ($cat = self::c('SITE_CATEGORY', '')) $org['additionalType'] = $cat;
        if ($email = self::c('SITE_CONTACT_EMAIL', '')) {
            $cp = ['@type' => 'ContactPoint', 'contactType' => self::c('SITE_CONTACT_TYPE', 'customer service'), 'email' => $email];
            if ($tel = self::c('SITE_CONTACT_PHONE', '')) $cp['telephone'] = $tel;
            $org['contactPoint'] = $cp;
        }
        if (defined('SITE_ADDRESS') && is_array(constant('SITE_ADDRESS'))) {
            $org['address'] = ['@type' => 'PostalAddress'] + constant('SITE_ADDRESS');
        }
        if (($rv = self::c('SITE_RATING', '')) !== '' && ($rc = self::c('SITE_RATING_COUNT', '')) !== '') {
            $org['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => $rv, 'ratingCount' => $rc];
        }

        $graph = [
            ['@type' => 'WebSite', '@id' => $base . '/#website', 'name' => $site, 'url' => $base, 'description' => $desc],
            $org,
            [
                '@type'       => self::c('SITE_PAGE_TYPE', 'WebPage'),
                '@id'         => $canon . '#webpage',
                'url'         => $canon,
                'name'        => (string) ($ctx['pageTitle'] ?? ''),
                'description' => $desc,
                'isPartOf'    => ['@id' => $base . '/#website'],
                'publisher'   => ['@id' => $orgId],
            ],
        ];
        foreach (($ctx['extra'] ?? []) as $node) {
            if (is_array($node)) $graph[] = $node;
        }
        return $graph;
    }

    private static function c(string $k, $default)
    {
        return (defined($k) && constant($k) !== '') ? constant($k) : $default;
    }

    private static function arr(string $k): array
    {
        if (!defined($k)) return [];
        $v = constant($k);
        if (is_array($v)) return $v;
        return array_filter(array_map('trim', explode(',', (string) $v)));
    }
}
