<?php include 'layouts/header.php'; ?>

<!-- ── Hero ────────────────────────────────────────────────────────────────── -->
<section aria-labelledby="hero-heading" class="p-5 mb-5 bg-dark text-white rounded-3">
    <div class="container-fluid py-4">
        <h1 id="hero-heading" class="display-5 fw-bold mb-3">
            TiCore PHP Framework
            <small class="fs-5 fw-normal text-secondary ms-2">v<?php echo e($framework_version); ?></small>
        </h1>
        <p class="col-md-9 fs-5 mb-3">
            The only open-source PHP framework with a <strong>complete SEO suite built in</strong> —
            canonical URLs, Open Graph, Twitter/X cards, Schema.org JSON-LD, og:video, og:logo,
            fb:app_id, and Google Analytics 4, all auto-generated from the layout with
            <strong>zero extra packages</strong>. Secure, lightweight, PHP 8.4+ native, and
            WCAG-accessible out of the box.
        </p>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/features" class="btn btn-primary btn-lg">Server Features</a>
            <a href="/compare" class="btn btn-outline-light btn-lg">Compare Frameworks</a>
            <span class="btn btn-outline-secondary btn-lg disabled" aria-label="Current environment: <?php echo e(strtoupper($app_env)); ?>">
                <?php echo e(strtoupper($app_env)); ?>
            </span>
        </div>
    </div>
</section>

<!-- ── Feature overview cards ──────────────────────────────────────────────── -->
<section aria-labelledby="features-heading" class="mb-5">
    <h2 id="features-heading" class="mb-4">What TiCore Includes</h2>
    <div class="row g-4">

        <div class="col-md-4">
            <article class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title h5">&#128274; Security First</h3>
                    <p class="card-text text-muted">
                        CSRF token generation and verification, output XSS escaping via
                        <code>e()</code>, and cryptographic session handling built into the core.
                        All sensitive files live outside the public web root.
                    </p>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title h5">&#128640; Smart Router</h3>
                    <p class="card-text text-muted">
                        Four-tier dispatch: manual routes &rarr; auto-discovered controllers &rarr;
                        PageController fallback &rarr; 404. Add a view file and it becomes a live
                        page instantly with zero configuration.
                    </p>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title h5">&#128221; Structured Logging</h3>
                    <p class="card-text text-muted">
                        Six-level log system (CRITICAL &rarr; DEBUG) with daily log rotation stored
                        outside the web root. Log verbosity is tunable per environment via
                        <code>LOG_LEVEL</code> in <code>.env</code>.
                    </p>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title h5">&#128202; Database Ready</h3>
                    <p class="card-text text-muted">
                        Optional PDO layer with prepared statements, utf8mb4 charset, and
                        exception-mode error handling. Enable with one flag in
                        <code>config.php</code> — no changes elsewhere required.
                    </p>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title h5">&#128269; SEO &amp; Open Graph</h3>
                    <p class="card-text text-muted">
                        Full per-page SEO built into the layout: canonical URLs, Open Graph,
                        Twitter/X cards, og:video, og:logo, fb:app_id, Schema.org JSON-LD,
                        Google Tag Manager, and WCAG accessibility — all via controller variables.
                    </p>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title h5">&#9881;&#65039; Zero-Config Pages</h3>
                    <p class="card-text text-muted">
                        Drop a <code>.php</code> view file into <code>templates/default/</code>
                        and the PageController serves it automatically — ideal for static or
                        mostly-static content without writing a dedicated controller.
                    </p>
                </div>
            </article>
        </div>

    </div>
</section>

<!-- ── What is TiCore ──────────────────────────────────────────────────────── -->
<section aria-labelledby="what-heading" class="mb-5">
    <div class="row g-5 align-items-start">
        <div class="col-lg-7">
            <h2 id="what-heading">What is TiCore?</h2>
            <p>
                TiCore is a minimal, security-first PHP framework that keeps your application logic
                entirely <strong>outside the public web root</strong>. The browser can only reach
                <code>www/</code>; everything else — controllers, templates, logs, credentials — lives
                in <code>TiCore/</code> where the web server cannot serve it directly.
            </p>
            <p>
                There are no magic globals, no auto-wiring surprises, and no framework lock-in beyond
                a handful of small core classes. Every piece of the stack is readable and replaceable.
            </p>

            <h3 class="mt-4">Architecture — Four-Tier Router</h3>
            <p>
                Requests hit <code>www/index.php</code>, which bootstraps the framework and hands off
                to the <strong>Router</strong>. The Router resolves the URI using a four-tier cascade:
            </p>
            <ol>
                <li><strong>Manual routes</strong> — explicit URI-to-controller mappings you define in code.</li>
                <li><strong>Auto-discovered controllers</strong> — a URI of <code>/login</code> automatically
                    maps to <code>LoginController</code> if the file exists.</li>
                <li><strong>PageController fallback</strong> — if no dedicated controller exists but a
                    matching view file does, <code>PageController</code> renders it directly.</li>
                <li><strong>404</strong> — served cleanly if nothing matches.</li>
            </ol>
        </div>
        <div class="col-lg-5">
            <div class="bg-dark text-light rounded-3 p-4" role="img" aria-label="TiCore directory layout diagram">
                <p class="text-secondary small mb-2 font-monospace"># Directory layout</p>
                <pre class="text-light mb-0" style="font-size:.8rem; overflow-x:auto;">project/
├── TiCore/               ← private
│   ├── .env
│   ├── .env-example
│   ├── .v                ← version
│   ├── config.php
│   ├── src/
│   │   ├── Controllers/
│   │   ├── Core/
│   │   └── functions_global.php
│   ├── templates/
│   │   └── default/
│   └── logs/
└── www/                  ← public
    ├── .htaccess
    ├── index.php
    └── assets/</pre>
            </div>
        </div>
    </div>
</section>

<!-- ── PageController ──────────────────────────────────────────────────────── -->
<section aria-labelledby="pagecontroller-heading" class="mb-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 id="pagecontroller-heading" class="card-title h4">&#9881;&#65039; Zero-Config Pages with PageController</h2>
            <p class="card-text">
                Most web projects have a handful of pages that are purely informational —
                an <em>About</em>, a <em>Privacy Policy</em>, a <em>Terms of Service</em>.
                Writing a dedicated controller for each is unnecessary boilerplate.
            </p>
            <p class="card-text">
                <strong>PageController</strong> solves this. The Router's Case D fallback checks
                whether a view file named after the URI exists in
                <code>templates/default/</code>. If it does, <code>PageController::show()</code>
                renders it automatically — no controller file needed.
            </p>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <div class="bg-light rounded p-3 font-monospace small" role="group" aria-label="Shell commands to create a static page">
                        <p class="text-muted mb-1"># Add a new static page</p>
                        <code>touch TiCore/templates/default/privacy.php</code>
                        <p class="text-muted mt-2 mb-1"># It&rsquo;s now live at:</p>
                        <code>https://yoursite.com/privacy</code>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded p-3 font-monospace small" role="group" aria-label="PHP code to set SEO meta in a static template">
                        <p class="text-muted mb-1"># Set SEO meta inside the template</p>
                        <code>&lt;?php</code><br>
                        <code>$meta_description = 'Our privacy policy.';</code><br>
                        <code>$meta_robots = 'noindex, follow';</code><br>
                        <code>?&gt;</code><br>
                        <code>&lt;?php include 'layouts/header.php'; ?&gt;</code>
                    </div>
                </div>
            </div>
            <p class="card-text mt-3 mb-0">
                You can still add business logic by creating a real controller later — the router
                will prefer it over the PageController fallback automatically.
            </p>
        </div>
    </div>
</section>

<!-- ── Core features checklist ─────────────────────────────────────────────── -->
<section aria-labelledby="core-features-heading" class="mb-5">
    <h2 id="core-features-heading" class="mb-3">Core Features</h2>
    <div class="row g-3">
        <div class="col-md-6">
            <ul class="list-group list-group-flush" role="list">
                <li class="list-group-item" role="listitem">&#128274; CSRF token generation &amp; verification</li>
                <li class="list-group-item" role="listitem">&#128221; XSS output escaping (<code>e()</code> helper)</li>
                <li class="list-group-item" role="listitem">&#128640; Four-tier smart router</li>
                <li class="list-group-item" role="listitem">&#128202; Optional PDO database layer</li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="list-group list-group-flush" role="list">
                <li class="list-group-item" role="listitem">&#128196; Six-level structured logger (0–5)</li>
                <li class="list-group-item" role="listitem">&#128233; PHPMailer 7 included via Composer</li>
                <li class="list-group-item" role="listitem">&#128269; Automatic SEO sitemap (optional)</li>
                <li class="list-group-item" role="listitem">&#9881;&#65039; Zero-config static page rendering</li>
            </ul>
        </div>
    </div>
</section>

<!-- ── SEO & Open Graph Documentation ─────────────────────────────────────── -->
<section aria-labelledby="seo-heading" class="mb-5">
    <h2 id="seo-heading" class="mb-1">SEO &amp; Open Graph — No Other Framework Does This</h2>
    <p class="text-muted mb-2">
        Laravel, Symfony, CodeIgniter, and Slim all require third-party packages or manual tag
        management for SEO. TiCore builds the entire suite directly into the layout — nothing
        to install, nothing to configure, nothing to forget.
    </p>
    <p class="text-muted mb-4">
        Every page automatically gets canonical URLs, full Open Graph, Twitter/X cards, and
        Schema.org JSON-LD structured data. Pass variables from any controller or static template —
        unset variables emit no tags at all, so your output is always clean.
    </p>

    <!-- Always-generated tags -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white fw-semibold" id="seo-auto-heading">
            &#9989; Always Generated — no setup required
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 font-monospace small"
                       aria-labelledby="seo-auto-heading">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Tag</th>
                            <th scope="col">Value / Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>&lt;title&gt;</td><td>$title + site name, or site name alone</td></tr>
                        <tr><td>meta description</td><td>$meta_description or site-wide default</td></tr>
                        <tr><td>meta robots</td><td>$meta_robots or <em>index, follow</em></td></tr>
                        <tr><td>meta author</td><td>Tuxxin — tuxxin.com</td></tr>
                        <tr><td>meta generator</td><td>TiCore + version from .v</td></tr>
                        <tr><td>meta theme-color</td><td>#212529</td></tr>
                        <tr><td>link canonical</td><td>BASE_URL + REQUEST_URI (overridable via $canonical)</td></tr>
                        <tr><td>og:type</td><td>$og_type or <em>website</em></td></tr>
                        <tr><td>og:site_name</td><td>SITE_TITLE constant</td></tr>
                        <tr><td>og:title</td><td>Same as &lt;title&gt;</td></tr>
                        <tr><td>og:description</td><td>Same as meta description</td></tr>
                        <tr><td>og:url</td><td>Same as canonical</td></tr>
                        <tr><td>og:image</td><td>$og_image or /assets/images/og-default.png</td></tr>
                        <tr><td>og:image:width / height</td><td>$og_image_width / $og_image_height or 1200&times;630</td></tr>
                        <tr><td>og:logo</td><td>$og_logo or SITE_LOGO constant (logo-v2.png)</td></tr>
                        <tr><td>twitter:card</td><td>$twitter_card or <em>summary_large_image</em></td></tr>
                        <tr><td>twitter:site</td><td>$twitter_site or <em>@tuxxin</em></td></tr>
                        <tr><td>twitter:title / description / image</td><td>Mirrors OG values</td></tr>
                        <tr><td>JSON-LD (Schema.org)</td><td>WebSite + Organization (logo + sameAs) + WebPage @graph</td></tr>
                        <tr><td>Google Analytics 4</td><td>GA4_ID constant in config.php (gtag.js)</td></tr>
                        <tr><td>link preconnect</td><td>cdn.jsdelivr.net always; googletagmanager.com + google-analytics.com when GA4_ID set</td></tr>
                        <tr><td>link sitemap</td><td>When SITEMAP_ENABLED = true</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Conditional tags -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white fw-semibold" id="seo-cond-heading">
            &#128196; Conditional — only rendered when the variable is set
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 font-monospace small"
                       aria-labelledby="seo-cond-heading">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Variable</th>
                            <th scope="col">Tags emitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>$og_video</td><td>og:video, og:video:type (default mp4), og:video:width, og:video:height</td></tr>
                        <tr><td>$fb_app_id</td><td>fb:app_id + preconnect to connect.facebook.net</td></tr>
                        <tr><td>$google_fonts_url</td><td>preconnect to fonts.googleapis.com + fonts.gstatic.com + &lt;link&gt; stylesheet</td></tr>
                        <tr><td>$gtm_id</td><td>preconnect to googletagmanager.com + full GTM head snippet + noscript fallback</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Code example -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white fw-semibold" id="seo-example-heading">
            &#128196; Controller Example — passing SEO variables to the layout
        </div>
        <div class="card-body p-0">
            <pre class="mb-0 p-4 text-white bg-dark rounded-bottom"
                 style="font-size:.82rem; overflow-x:auto;"
                 role="region" aria-labelledby="seo-example-heading"><code>// In any controller:
view('product', [
    // Basic
    'title'            =&gt; 'Black Invicta 44mm Watch',
    'meta_description' =&gt; 'Invicta Prestige X: 44mm watch with Flame Fusion Crystal…',
    'meta_robots'      =&gt; 'index, follow',
    'canonical'        =&gt; 'https://example.com/products/invicta-44mm',

    // Open Graph
    'og_type'          =&gt; 'product',
    'og_image'         =&gt; 'https://example.com/imgs/og/product-1.webp',
    'og_image_width'   =&gt; 1200,
    'og_image_height'  =&gt; 630,
    'og_logo'          =&gt; 'https://example.com/assets/images/logo.webp',

    // Video (optional — omit if no video)
    'og_video'         =&gt; 'https://example.com/uploads/videos/product-1.mp4',
    'og_video_type'    =&gt; 'video/mp4',
    'og_video_width'   =&gt; 1080,
    'og_video_height'  =&gt; 1080,

    // Facebook
    'fb_app_id'        =&gt; '816733251417750',

    // Google (optional — omit if not using)
    'gtm_id'           =&gt; 'GTM-XXXXXXX',
    'google_fonts_url' =&gt; 'https://fonts.googleapis.com/css2?family=Inter&amp;display=swap',
]);

// In a static template (PageController page), set variables before include:
// $meta_description = 'Our privacy policy.';
// $meta_robots = 'noindex, follow';
// Then: &lt;?php include 'layouts/header.php'; ?&gt;</code></pre>
        </div>
    </div>

</section>

<?php include 'layouts/footer.php'; ?>
