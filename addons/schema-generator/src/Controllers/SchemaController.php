<?php
namespace Tuxxin\TiCore\Addons\SchemaGenerator\Controllers;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

/**
 * Stateless schema.org / JSON-LD generator. GET renders an empty form; POST
 * (CSRF + honeypot guarded) re-renders the form WITH a generated JSON-LD block
 * and a copy-paste PHP config snippet. Nothing is persisted, written, or logged.
 */
final class SchemaController
{
    private function types(): array
    {
        return config('schema-generator')['types'] ?? ['Organization', 'LocalBusiness', 'Product', 'Article', 'WebSite'];
    }

    /** GET /tools/schema */
    public function show(): Response
    {
        return Response::view('schema-generator::form', [
            'title'   => 'Schema.org Generator',
            'types'   => $this->types(),
            'in'      => $this->emptyInput(),
            'jsonld'  => null,
            'snippet' => null,
            'error'   => null,
        ]);
    }

    /** POST /tools/schema */
    public function generate(Request $req): Response
    {
        $types = $this->types();

        // Honeypot: a bot fills the hidden "website" field. Humans never see it.
        if (trim((string) $req->input('website', '')) !== '') {
            // Silently re-render empty form — do NOT log the values (stateless tool).
            return Response::view('schema-generator::form', [
                'title' => 'Schema.org Generator', 'types' => $types,
                'in' => $this->emptyInput(), 'jsonld' => null, 'snippet' => null, 'error' => null,
            ]);
        }

        $in = [
            'type'        => (string) $req->input('type', ''),
            'name'        => trim((string) $req->input('name', '')),
            'url'         => trim((string) $req->input('url', '')),
            'logo'        => trim((string) $req->input('logo', '')),
            'description' => trim((string) $req->input('description', '')),
            'category'    => trim((string) $req->input('category', '')),
            'sameAs'      => trim((string) $req->input('sameAs', '')),
            'email'       => trim((string) $req->input('email', '')),
        ];

        if (!in_array($in['type'], $types, true)) {
            return Response::view('schema-generator::form', [
                'title' => 'Schema.org Generator', 'types' => $types,
                'in' => $in, 'jsonld' => null, 'snippet' => null,
                'error' => 'Pick a supported schema type.',
            ], 422);
        }

        $schema = $this->buildSchema($in);

        // Pretty JSON; entities in string values are escaped at render time in the view.
        $json = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $jsonld  = "<script type=\"application/ld+json\">\n" . $json . "\n</script>";
        $snippet = $this->phpSnippet($schema);

        return Response::view('schema-generator::form', [
            'title'   => 'Schema.org Generator',
            'types'   => $types,
            'in'      => $in,
            'jsonld'  => $jsonld,
            'snippet' => $snippet,
            'error'   => null,
        ]);
    }

    private function emptyInput(): array
    {
        return ['type' => '', 'name' => '', 'url' => '', 'logo' => '',
                'description' => '', 'category' => '', 'sameAs' => '', 'email' => ''];
    }

    /** Build the schema.org array from posted fields (only non-empty keys). */
    private function buildSchema(array $in): array
    {
        $s = ['@context' => 'https://schema.org', '@type' => $in['type']];

        if ($in['name'] !== '')        $s['name'] = $in['name'];
        if ($in['url'] !== '')         $s['url'] = $in['url'];
        if ($in['description'] !== '') $s['description'] = $in['description'];

        // logo applies cleanly to Organization/LocalBusiness; harmless elsewhere.
        if ($in['logo'] !== '') {
            $s['logo'] = $in['logo'];
            if (in_array($in['type'], ['Product', 'Article', 'WebSite'], true)) {
                $s['image'] = $in['logo'];
            }
        }

        if ($in['category'] !== '') {
            $s['category'] = $in['category'];
        }

        if ($in['email'] !== '') {
            if (in_array($in['type'], ['Organization', 'LocalBusiness'], true)) {
                $s['contactPoint'] = [
                    '@type'       => 'ContactPoint',
                    'email'       => $in['email'],
                    'contactType' => 'customer service',
                ];
            } else {
                $s['email'] = $in['email'];
            }
        }

        if ($in['sameAs'] !== '') {
            $links = array_values(array_filter(array_map('trim', explode(',', $in['sameAs'])), fn($v) => $v !== ''));
            if ($links) $s['sameAs'] = $links;
        }

        return $s;
    }

    /** A copy-paste PHP array snippet mirroring the schema (var_export, tidied). */
    private function phpSnippet(array $schema): string
    {
        $php = var_export($schema, true);
        // var_export uses array(...) — convert to short [] syntax for a tidy snippet.
        $php = preg_replace('/=>\s*\n\s*array \(/', '=> [', $php);
        $php = preg_replace('/\barray \(/', '[', $php);
        $php = preg_replace('/^([ ]*)\)/m', '$1]', $php);
        return "<?php\n// schema.org JSON-LD for this page (stateless — generated, not stored).\n"
             . '$schema = ' . $php . ";\n"
             . "// echo '<script type=\"application/ld+json\">' . json_encode(\$schema, JSON_UNESCAPED_SLASHES) . '</script>';\n";
    }
}
