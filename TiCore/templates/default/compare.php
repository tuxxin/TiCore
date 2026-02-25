<?php
$title            = 'TiCore vs Other PHP Frameworks';
$meta_description = 'Side-by-side comparison of TiCore, Laravel, Symfony, CodeIgniter, and Slim — SEO suite, accessibility, security, zero-config pages, and more.';
$meta_robots      = 'index, follow';
?>
<?php include 'layouts/header.php'; ?>

<!-- ── Page header ─────────────────────────────────────────────────────────── -->
<section aria-labelledby="compare-heading" class="mb-5">
    <h1 id="compare-heading" class="display-6 fw-bold">Framework Comparison</h1>
    <p class="lead text-muted">
        How TiCore stacks up against the most widely-used PHP frameworks on features that matter
        for production — particularly the built-in SEO and accessibility capabilities that no other
        framework ships out of the box.
    </p>
    <p class="text-muted small">
        &#9989; Built-in &nbsp;|&nbsp;
        <span class="text-warning">&#9888;&#65039; Partial / setup required</span> &nbsp;|&nbsp;
        <span class="text-danger">&#10060; Not included (package / manual)</span>
    </p>
</section>

<!-- ── SEO Suite comparison ────────────────────────────────────────────────── -->
<section aria-labelledby="seo-compare-heading" class="mb-5">
    <h2 id="seo-compare-heading" class="mb-3">SEO &amp; Open Graph Suite</h2>
    <p class="text-muted mb-3">
        TiCore is the <strong>only framework</strong> that ships a complete SEO suite in its default
        layout. Every other framework requires third-party packages (e.g. <code>artesaos/seotools</code>
        for Laravel) or fully manual tag management.
    </p>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle"
               aria-labelledby="seo-compare-heading">
            <caption class="visually-hidden">SEO feature comparison across PHP frameworks</caption>
            <thead class="table-dark">
                <tr>
                    <th scope="col">Feature</th>
                    <th scope="col" class="text-center text-warning">TiCore</th>
                    <th scope="col" class="text-center">Laravel</th>
                    <th scope="col" class="text-center">Symfony</th>
                    <th scope="col" class="text-center">CodeIgniter</th>
                    <th scope="col" class="text-center">Slim</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Auto canonical URL</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Open Graph (og:title / description / url)</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>og:image with fallback default</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>og:logo</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>og:video (type / width / height)</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Twitter / X card (summary_large_image)</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>fb:app_id meta tag</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Schema.org JSON-LD @graph (WebSite + Org + WebPage)</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Google Analytics 4 (built-in)</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Google Tag Manager (built-in)</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>DNS preconnects (auto per active feature)</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Auto-generating sitemap.xml</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-warning">&#9888;&#65039; Package</td>
                    <td class="text-center text-warning">&#9888;&#65039; Bundle</td>
                    <td class="text-center text-warning">&#9888;&#65039; Package</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>SEO packages required</td>
                    <td class="text-center fw-bold text-success">0</td>
                    <td class="text-center text-danger">artesaos/seotools</td>
                    <td class="text-center text-danger">manual</td>
                    <td class="text-center text-danger">manual</td>
                    <td class="text-center text-danger">manual</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- ── Security & architecture comparison ─────────────────────────────────── -->
<section aria-labelledby="security-compare-heading" class="mb-5">
    <h2 id="security-compare-heading" class="mb-3">Security &amp; Architecture</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle"
               aria-labelledby="security-compare-heading">
            <caption class="visually-hidden">Security and architecture comparison across PHP frameworks</caption>
            <thead class="table-dark">
                <tr>
                    <th scope="col">Feature</th>
                    <th scope="col" class="text-center text-warning">TiCore</th>
                    <th scope="col" class="text-center">Laravel</th>
                    <th scope="col" class="text-center">Symfony</th>
                    <th scope="col" class="text-center">CodeIgniter</th>
                    <th scope="col" class="text-center">Slim</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>CSRF protection</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-warning">&#9888;&#65039; Bundle</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>XSS output escaping helper</td>
                    <td class="text-center">&#9989; <code>e()</code></td>
                    <td class="text-center">&#9989; Blade</td>
                    <td class="text-center">&#9989; Twig</td>
                    <td class="text-center text-warning">&#9888;&#65039; Partial</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Logic outside public web root</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-warning">&#9888;&#65039; Optional</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Six-level structured logger</td>
                    <td class="text-center">&#9989; Native</td>
                    <td class="text-center text-warning">&#9888;&#65039; Monolog</td>
                    <td class="text-center text-warning">&#9888;&#65039; Monolog</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>Zero-config page serving</td>
                    <td class="text-center">&#9989; PageController</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>WCAG accessibility in core templates</td>
                    <td class="text-center">&#9989;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                    <td class="text-center text-danger">&#10060;</td>
                </tr>
                <tr>
                    <td>PHP 8.4+ native</td>
                    <td class="text-center">&#9989; 8.4+</td>
                    <td class="text-center text-warning">&#9888;&#65039; 8.2+</td>
                    <td class="text-center text-warning">&#9888;&#65039; 8.2+</td>
                    <td class="text-center text-warning">&#9888;&#65039; 8.1+</td>
                    <td class="text-center text-warning">&#9888;&#65039; 8.1+</td>
                </tr>
                <tr>
                    <td>License</td>
                    <td class="text-center">MIT</td>
                    <td class="text-center">MIT</td>
                    <td class="text-center">MIT</td>
                    <td class="text-center">MIT</td>
                    <td class="text-center">MIT</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- ── Summary callout ─────────────────────────────────────────────────────── -->
<section aria-labelledby="summary-heading" class="mb-5">
    <div class="card border-0 bg-dark text-white">
        <div class="card-body p-4">
            <h2 id="summary-heading" class="card-title h4 mb-3">Why TiCore Is Different</h2>
            <p class="card-text">
                Every major PHP framework treats SEO as an afterthought — something you bolt on
                with a Composer package after the fact. TiCore was built from day one with a
                production-grade SEO layout that any developer can use immediately, without reading
                package documentation, writing Blade components, or maintaining a custom base template.
            </p>
            <p class="card-text mb-0">
                The result: every page on a TiCore site gets correct canonical URLs, rich Open Graph
                tags, Twitter/X cards, Google Analytics, and Schema.org JSON-LD structured data —
                with a single controller call and zero extra dependencies.
            </p>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <a href="/" class="btn btn-primary">Back to Home</a>
        <a href="/features" class="btn btn-outline-secondary">Server Features</a>
    </div>
</section>

<?php include 'layouts/footer.php'; ?>
