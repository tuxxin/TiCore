<?php
// ── Per-page SEO / meta variables ─────────────────────────────────────────────
// Set any of these in your controller before calling view() — unset variables
// are simply not rendered (no empty tags emitted).
//
// BASIC
//   $title              string   Page title (site name appended automatically)
//   $meta_description   string   <meta name="description"> — 150–160 chars ideal
//   $meta_robots        string   e.g. 'noindex, nofollow'   default: 'index, follow'
//   $canonical          string   Override the auto-built canonical URL
//
// OPEN GRAPH — image
//   $og_image           string   Absolute URL to share image (1200×630 recommended)
//   $og_image_width     int      Image width  in px  (default 1200)
//   $og_image_height    int      Image height in px  (default 630)
//   $og_image_alt       string   Alt text for og:image  (default: site name)
//   $og_logo            string   Absolute URL to logo image
//
// OPEN GRAPH — video
//   $og_video           string   Absolute URL to video file (mp4 recommended)
//   $og_video_type      string   MIME type   (default: video/mp4)
//   $og_video_width     int      Video width  in px
//   $og_video_height    int      Video height in px
//
// OPEN GRAPH — type
//   $og_type            string   'website' | 'article' | 'product' …  (default: website)
//
// FACEBOOK
//   $fb_app_id          string   Facebook App ID — enables fb:app_id meta tag
//
// TWITTER / X
//   $twitter_card       string   'summary_large_image' | 'summary'  (default: summary_large_image)
//   $twitter_site       string   @handle for the site account   (default: @tuxxin)
//
// THIRD-PARTY PRECONNECTS / INTEGRATIONS
//   $google_fonts_url   string   Full Google Fonts <link href> URL — emits preconnects + link
//   $gtm_id             string   Google Tag Manager container ID (e.g. GTM-XXXXXXX)
// ─────────────────────────────────────────────────────────────────────────────

$_tcSiteName    = defined('SITE_TITLE') ? SITE_TITLE : 'TiCore';
$_tcBaseUrl     = defined('BASE_URL')   ? rtrim(BASE_URL, '/') : '';

$_tcPageTitle   = isset($title) && $title !== ''
    ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' — ' . $_tcSiteName
    : $_tcSiteName;

$_tcDescription = isset($meta_description) && $meta_description !== ''
    ? htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8')
    : 'TiCore is a secure, lightweight PHP 8.4+ MVC framework by Tuxxin — fast routing, CSRF/XSS protection, and structured logging out of the box.';

$_tcRobots      = isset($meta_robots) ? $meta_robots : 'index, follow';
$_tcOgType      = isset($og_type)     ? $og_type     : 'website';

$_tcRequestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$_tcCanonical   = isset($canonical) && $canonical !== ''
    ? $canonical
    : $_tcBaseUrl . $_tcRequestPath;

// OG image — falls back to the site logo so shares always have something to show
$_tcOgImage       = isset($og_image)        && $og_image        !== '' ? $og_image        : $_tcBaseUrl . '/assets/images/TiCore-OG.webp';
$_tcOgImageWidth  = isset($og_image_width)  && $og_image_width  !== '' ? (int)$og_image_width  : 1200;
$_tcOgImageHeight = isset($og_image_height) && $og_image_height !== '' ? (int)$og_image_height : 630;
$_tcOgImageAlt    = isset($og_image_alt)    && $og_image_alt    !== '' ? htmlspecialchars($og_image_alt, ENT_QUOTES, 'UTF-8') : htmlspecialchars($_tcSiteName, ENT_QUOTES, 'UTF-8');

// Twitter
$_tcTwitterCard = isset($twitter_card) && $twitter_card !== '' ? $twitter_card : 'summary_large_image';
$_tcTwitterSite = isset($twitter_site) && $twitter_site !== '' ? $twitter_site : '@tuxxin';

// Framework version for JSON-LD / meta generator
$_tcVersion = file_exists(CORE_PATH . '/.v') ? trim(file_get_contents(CORE_PATH . '/.v')) : '';

// Site logo, Facebook URL, GA4 (from config constants)
$_tcSiteLogo    = defined('SITE_LOGO')    && SITE_LOGO    !== '' ? SITE_LOGO    : '';
$_tcFacebookUrl = defined('FACEBOOK_URL') && FACEBOOK_URL !== '' ? FACEBOOK_URL : '';
$_tcGa4Id       = defined('GA4_ID')       && GA4_ID       !== '' ? GA4_ID       : '';

// Active nav helper
$_tcCurrentPath = trim($_tcRequestPath, '/');
$_tcNavAttrs = static function (string $href) use ($_tcCurrentPath): string {
    $path   = trim($href, '/');
    $active = ($path === '' && $_tcCurrentPath === '')
           || ($path !== '' && $_tcCurrentPath === $path);
    return $active
        ? 'class="nav-link active" aria-current="page"'
        : 'class="nav-link"';
};

// Escape helper (shorthand for this file)
$_e = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

// ── Tuxxin Suite nav — fetched from JSON, cached 1 hour ──────────────────────
$_tcSuiteCache = sys_get_temp_dir() . '/tuxxin_suite_nav.json';
$_tcSuiteItems = [];
if (file_exists($_tcSuiteCache) && (time() - filemtime($_tcSuiteCache)) < 3600) {
    $_tcSuiteItems = json_decode(file_get_contents($_tcSuiteCache), true) ?: [];
} else {
    $_tcSuiteCtx = stream_context_create(['http' => ['timeout' => 3]]);
    $_tcSuiteRaw = @file_get_contents('https://tuxxin.com/tuxxin_suite.json', false, $_tcSuiteCtx);
    if ($_tcSuiteRaw) {
        $_tcSuiteItems = json_decode($_tcSuiteRaw, true) ?: [];
        file_put_contents($_tcSuiteCache, $_tcSuiteRaw);
    }
}
$_tcNavItemAttrs = static function (array $attrs): string {
    return implode(' ', array_map(
        fn($k, $v) => htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . '"',
        array_keys($attrs), $attrs
    ));
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ── DNS prefetch / preconnect ────────────────────────────────────── -->
    <!--  jsdelivr is always used for Bootstrap -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <?php if (isset($google_fonts_url) && $google_fonts_url !== ''): ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php endif; ?>

    <?php if (isset($fb_app_id) && $fb_app_id !== ''): ?>
    <link rel="preconnect" href="https://connect.facebook.net">
    <?php endif; ?>

    <?php if ((isset($gtm_id) && $gtm_id !== '') || $_tcGa4Id !== ''): ?>
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <?php endif; ?>
    <?php if ($_tcGa4Id !== ''): ?>
    <link rel="preconnect" href="https://www.google-analytics.com">
    <?php endif; ?>

    <!-- ── Google Tag Manager (head snippet) ────────────────────────────── -->
    <?php if (isset($gtm_id) && $gtm_id !== ''): ?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo $_e($gtm_id); ?>');</script>
    <?php endif; ?>

    <!-- ── Google Analytics 4 ───────────────────────────────────────────── -->
    <?php if ($_tcGa4Id !== ''): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $_e($_tcGa4Id); ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo $_e($_tcGa4Id); ?>');</script>
    <?php endif; ?>

    <!-- ── Primary meta ─────────────────────────────────────────────────── -->
    <title><?php echo $_tcPageTitle; ?></title>
    <meta name="description" content="<?php echo $_tcDescription; ?>">
    <meta name="robots"      content="<?php echo $_e($_tcRobots); ?>">
    <meta name="author"      content="Tuxxin — tuxxin.com">
    <meta name="generator"   content="TiCore <?php echo $_e($_tcVersion); ?>">
    <meta name="theme-color" content="#212529">

    <!-- ── Canonical ────────────────────────────────────────────────────── -->
    <link rel="canonical" href="<?php echo $_e($_tcCanonical); ?>">

    <!-- ── Sitemap discovery ────────────────────────────────────────────── -->
    <?php if (defined('SITEMAP_ENABLED') && SITEMAP_ENABLED): ?>
    <link rel="sitemap" type="application/xml" href="<?php echo $_e($_tcBaseUrl); ?>/sitemap.xml">
    <?php endif; ?>

    <!-- ── Open Graph — core ────────────────────────────────────────────── -->
    <meta property="og:type"        content="<?php echo $_e($_tcOgType); ?>">
    <meta property="og:site_name"   content="<?php echo $_e($_tcSiteName); ?>">
    <meta property="og:title"       content="<?php echo $_tcPageTitle; ?>">
    <meta property="og:description" content="<?php echo $_tcDescription; ?>">
    <meta property="og:url"         content="<?php echo $_e($_tcCanonical); ?>">
    <meta property="og:locale"      content="en_US">

    <!-- ── Open Graph — image ───────────────────────────────────────────── -->
    <meta property="og:image"        content="<?php echo $_e($_tcOgImage); ?>">
    <meta property="og:image:width"  content="<?php echo $_tcOgImageWidth; ?>">
    <meta property="og:image:height" content="<?php echo $_tcOgImageHeight; ?>">
    <meta property="og:image:alt"    content="<?php echo $_tcOgImageAlt; ?>">

    <!-- ── Open Graph — logo ────────────────────────────────────────────── -->
    <?php $_tcEmitLogo = (isset($og_logo) && $og_logo !== '') ? $og_logo : $_tcSiteLogo; ?>
    <?php if ($_tcEmitLogo !== ''): ?>
    <meta property="og:logo" content="<?php echo $_e($_tcEmitLogo); ?>">
    <?php endif; ?>

    <?php if (isset($og_video) && $og_video !== ''): ?>
    <!-- ── Open Graph — video ───────────────────────────────────────────── -->
    <meta property="og:video"        content="<?php echo $_e($og_video); ?>">
    <meta property="og:video:type"   content="<?php echo $_e(isset($og_video_type) && $og_video_type !== '' ? $og_video_type : 'video/mp4'); ?>">
    <?php if (isset($og_video_width)  && $og_video_width  !== ''): ?><meta property="og:video:width"  content="<?php echo (int)$og_video_width; ?>"><?php endif; ?>
    <?php if (isset($og_video_height) && $og_video_height !== ''): ?><meta property="og:video:height" content="<?php echo (int)$og_video_height; ?>"><?php endif; ?>
    <?php endif; ?>

    <?php if (isset($fb_app_id) && $fb_app_id !== ''): ?>
    <!-- ── Facebook App ID ──────────────────────────────────────────────── -->
    <meta property="fb:app_id" content="<?php echo $_e($fb_app_id); ?>">
    <?php endif; ?>

    <!-- ── Twitter / X Card ─────────────────────────────────────────────── -->
    <meta name="twitter:card"        content="<?php echo $_e($_tcTwitterCard); ?>">
    <meta name="twitter:site"        content="<?php echo $_e($_tcTwitterSite); ?>">
    <meta name="twitter:title"       content="<?php echo $_tcPageTitle; ?>">
    <meta name="twitter:description" content="<?php echo $_tcDescription; ?>">
    <meta name="twitter:image"       content="<?php echo $_e($_tcOgImage); ?>">
    <meta name="twitter:image:alt"   content="<?php echo $_tcOgImageAlt; ?>">

    <!-- ── Structured data (Schema.org JSON-LD) ─────────────────────────── -->
    <script type="application/ld+json">
    <?php
    $_tcLd_org = [
        '@type' => 'Organization',
        '@id'   => 'https://tuxxin.com/#organization',
        'name'  => 'Tuxxin',
        'url'   => 'https://tuxxin.com',
    ];
    if ($_tcSiteLogo !== '') {
        $_tcLd_org['logo'] = ['@type' => 'ImageObject', 'url' => $_tcSiteLogo, 'width' => 306, 'height' => 338];
    }
    if ($_tcFacebookUrl !== '') {
        $_tcLd_org['sameAs'] = [$_tcFacebookUrl];
    }
    echo json_encode([
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'       => 'WebSite',
                '@id'         => $_tcBaseUrl . '/#website',
                'name'        => $_tcSiteName,
                'url'         => $_tcBaseUrl,
                'description' => html_entity_decode($_tcDescription, ENT_QUOTES, 'UTF-8'),
            ],
            $_tcLd_org,
            [
                '@type'       => 'WebPage',
                '@id'         => $_tcCanonical . '#webpage',
                'url'         => $_tcCanonical,
                'name'        => html_entity_decode($_tcPageTitle, ENT_QUOTES, 'UTF-8'),
                'description' => html_entity_decode($_tcDescription, ENT_QUOTES, 'UTF-8'),
                'isPartOf'    => ['@id' => $_tcBaseUrl . '/#website'],
                'publisher'   => ['@id' => 'https://tuxxin.com/#organization'],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    ?>
    </script>

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8156109007911677"
     crossorigin="anonymous"></script>

    <!-- ── Favicon ──────────────────────────────────────────────────────── -->
    <link rel="icon"             href="/assets/images/TiCore-Fav.png" type="image/png">
    <link rel="apple-touch-icon" href="/assets/images/TiCore-Fav.png" type="image/png">

    <!-- ── Stylesheets ──────────────────────────────────────────────────── -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <?php if (isset($google_fonts_url) && $google_fonts_url !== ''): ?>
    <link href="<?php echo $_e($google_fonts_url); ?>" rel="stylesheet">
    <?php endif; ?>

</head>
<body>
<?php if (isset($gtm_id) && $gtm_id !== ''): ?>
<!-- ── Google Tag Manager (noscript fallback) ───────────────────────────────── -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $_e($gtm_id); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>

<!-- ── Accessibility: skip navigation ──────────────────────────────────────── -->
<a class="visually-hidden-focusable position-absolute top-0 start-0 p-2 bg-white text-dark z-3"
   href="#main-content">
    Skip to main content
</a>

<!-- ── Site navigation ─────────────────────────────────────────────────────── -->
<header role="banner">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"
         aria-label="Primary navigation">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/"
               aria-label="TiCore PHP Framework — go to homepage">
                <?php if ($_tcSiteLogo !== ''): ?>
                <img src="/assets/images/TiCore-Logo-Icon.webp"
                     alt=""
                     height="40" width="36"
                     style="object-fit:contain;">
                <?php endif; ?>
                TiCore PHP Framework
            </a>

            <!-- Mobile toggle -->
            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#primaryNav"
                    aria-controls="primaryNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav links -->
            <div class="collapse navbar-collapse" id="primaryNav">
                <ul class="navbar-nav ms-auto" role="list">
                    <li class="nav-item" role="listitem">
                        <a <?php echo $_tcNavAttrs('/'); ?> href="/">Home</a>
                    </li>
                    <li class="nav-item" role="listitem">
                        <a <?php echo $_tcNavAttrs('/features'); ?> href="/features">Features</a>
                    </li>
                    <li class="nav-item" role="listitem">
                        <a <?php echo $_tcNavAttrs('/compare'); ?> href="/compare">Compare</a>
                    </li>
                    <li class="nav-item" role="listitem">
                        <button type="button" class="nav-link"
                                data-bs-toggle="modal"
                                data-bs-target="#tcGithubModal"
                                aria-haspopup="dialog"
                                aria-label="View TiCore on GitHub">
                            GitHub
                        </button>
                    </li>
                    <li class="nav-item dropdown" role="listitem">
                        <a class="nav-link dropdown-toggle"
                           href="#"
                           id="tuxxinSuiteDropdown"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                            Tuxxin Suite
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="tuxxinSuiteDropdown">
                            <?php foreach ($_tcSuiteItems as $_tcSuiteItem): ?>
                                <?php if (($_tcSuiteItem['type'] ?? '') === 'divider'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif (($_tcSuiteItem['type'] ?? '') === 'link'): ?>
                                    <?php
                                    $_tcSuiteAttrs     = $_tcSuiteItem['attributes'] ?? [];
                                    $_tcSuiteHref      = htmlspecialchars($_tcSuiteItem['href'] ?? '#', ENT_QUOTES, 'UTF-8');
                                    $_tcSuiteText      = htmlspecialchars(html_entity_decode($_tcSuiteItem['text'] ?? '', ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                                    $_tcSuiteIsCurrent = rtrim($_tcSuiteItem['href'] ?? '', '/') === rtrim($_tcBaseUrl, '/');
                                    if ($_tcSuiteIsCurrent) {
                                        $_tcSuiteAttrs['class'] = 'dropdown-item active';
                                        unset($_tcSuiteAttrs['target'], $_tcSuiteAttrs['rel']);
                                        $_tcSuiteHref = '/';
                                    }
                                    ?>
                                    <li>
                                        <a <?= $_tcNavItemAttrs($_tcSuiteAttrs) ?> href="<?= $_tcSuiteHref ?>">
                                            <?= $_tcSuiteText ?>
                                            <?php if ($_tcSuiteIsCurrent): ?><span class="visually-hidden">(current)</span><?php endif; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (empty($_tcSuiteItems)): ?>
                                <li><a class="dropdown-item" href="https://tuxxin.com" target="_blank" rel="noopener noreferrer">Back to Tuxxin.com &rarr;</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
</header>

<!-- ── Main content ─────────────────────────────────────────────────────────── -->
<main id="main-content" class="container mt-4" tabindex="-1">
