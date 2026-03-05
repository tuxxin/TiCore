# TiCore Framework

**Version:** 1.25
**PHP:** 8.4+
**License:** MIT
**Author:** [Tuxxin](https://tuxxin.com)

**Live Demo:** [ticore.tuxxin.com](https://ticore.tuxxin.com)

---

## Overview

TiCore (**Tuxxin Integrated Core**) is a secure, lightweight MVC framework for PHP 8.4+. It is the **only open-source PHP
framework with a complete SEO suite built into the layout** — canonical URLs, Open Graph,
Twitter/X cards, Schema.org JSON-LD, og:video, og:logo, fb:app_id, Google Analytics 4, and
auto-generating sitemap.xml, all included with zero extra packages.

Application logic lives entirely **outside the public web root** — only `www/` is web-accessible.

See how it compares: [ticore.tuxxin.com/compare](https://ticore.tuxxin.com/compare)

---

## What Makes TiCore Different

Every major PHP framework (Laravel, Symfony, CodeIgniter, Slim) requires third-party packages
for SEO. TiCore builds the entire suite into the default layout — nothing to install, nothing
to configure, nothing to forget.

| Built-in Feature | TiCore | Laravel | Symfony | CodeIgniter | Slim |
|------------------|:------:|:-------:|:-------:|:-----------:|:----:|
| Auto canonical URL | ✅ | ❌ | ❌ | ❌ | ❌ |
| Open Graph (full) | ✅ | ❌ | ❌ | ❌ | ❌ |
| Twitter/X card | ✅ | ❌ | ❌ | ❌ | ❌ |
| Schema.org JSON-LD | ✅ | ❌ | ❌ | ❌ | ❌ |
| og:video / og:logo | ✅ | ❌ | ❌ | ❌ | ❌ |
| Google Analytics 4 | ✅ | ❌ | ❌ | ❌ | ❌ |
| Auto sitemap.xml | ✅ | ⚠️ pkg | ⚠️ pkg | ⚠️ pkg | ❌ |
| WCAG accessibility | ✅ | ❌ | ❌ | ❌ | ❌ |
| Zero-config pages  | ✅ | ❌ | ❌ | ❌ | ❌ |
| SEO packages needed | **0** | ❌ | ❌ | ❌ | ❌ |

---

## Core Features

- **Security first** — CSRF tokens, XSS output escaping (`e()`), all logic outside web root
- **Smart four-tier router** — manual → auto-controller → PageController fallback → 404
- **Six-level logger** — CRITICAL / ERROR / WARNING / INFO / DEPRECATED / DEBUG (0–5)
- **Complete SEO suite** — full meta, OG, Twitter/X, JSON-LD, sitemap.xml auto-generated from layout
- **Optional PDO database** — enable with one flag; prepared statements, utf8mb4
- **Zero-config pages** — drop a view file, get a live URL instantly via PageController
- **WCAG accessible** — skip link, ARIA landmarks, `role` attributes in default templates
- **Environment-aware errors** — full stack traces in development, silent in production

---

## Directory Structure

```
project/
├── TiCore/                       ← private (not web-accessible)
│   ├── .env                      ← environment secrets (DB, SMTP)
│   ├── .env-example              ← documented template
│   ├── .v                        ← framework version
│   ├── config.php                ← constants (SITE_LOGO, GA4_ID, FACEBOOK_URL, etc.)
│   ├── src/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── FeaturesController.php
│   │   │   └── PageController.php
│   │   ├── Core/
│   │   │   ├── Router.php
│   │   │   ├── Logger.php
│   │   │   ├── Database.php
│   │   │   ├── DotEnv.php
│   │   │   └── Security.php
│   │   └── functions_global.php
│   ├── templates/default/
│   │   ├── home.php
│   │   ├── features.php
│   │   ├── compare.php
│   │   ├── 404.php
│   │   └── layouts/
│   │       ├── header.php        ← full SEO/OG/JSON-LD/GA4/a11y layout
│   │       └── footer.php
│   └── logs/                     ← daily log files (app-YYYY-MM-DD.log)
└── www/                          ← public web root
    ├── .htaccess
    ├── index.php
    └── assets/
```

---

## Configuration

### `.env` (secrets only)

```env
APP_ENV=development        # development | production
LOG_LEVEL=3                # 0=CRITICAL  1=ERROR  2=WARNING  3=INFO  4=DEPRECATED  5=DEBUG
DB_HOST=localhost
DB_NAME=ticore_db
DB_USER=ticore_user
DB_PASS=change_me
```

### `config.php` (non-secret constants, edit directly)

| Constant          | Default | Purpose                              |
|-------------------|---------|--------------------------------------|
| `SITE_TITLE`      | `TiCore Secure Framework` | `<title>` and JSON-LD |
| `BASE_URL`        | `https://ticore.tuxxin.com` | Canonical URLs, OG tags |
| `SITE_LOGO`       | `/assets/images/logo-v2.png` | Navbar, og:logo, JSON-LD |
| `FACEBOOK_URL`    | `https://facebook.com/tuxxin` | JSON-LD Organization sameAs |
| `GA4_ID`          | `G-9RF6FP6ZGX` | Google Analytics 4 |
| `DB_ENABLED`      | `false` | Enable/disable PDO layer |
| `SITEMAP_ENABLED` | `false` | Expose `/sitemap.xml` |

---

## Adding a Page

**Static page — no controller needed (PageController fallback):**

```bash
touch TiCore/templates/default/privacy.php
# Now live at https://yoursite.com/privacy
```

Set SEO meta inside the template before the layout include:

```php
<?php
$title            = 'Privacy Policy';
$meta_description = 'Our privacy policy.';
$meta_robots      = 'noindex, follow';
?>
<?php include 'layouts/header.php'; ?>
```

**Page with a dedicated controller:**

```php
// TiCore/src/Controllers/ContactController.php
namespace TiCore\Controllers;

class ContactController {
    public function index(): void {
        view('contact', [
            'title'            => 'Contact Us',
            'meta_description' => 'Get in touch with Tuxxin.',
        ]);
    }
}
```

---

## SEO Layout Variables

Pass any of these from your controller to `view()` — unset variables emit no tags:

```php
view('page', [
    'title'            => 'Page Title',
    'meta_description' => '150–160 char description',
    'og_type'          => 'article',
    'og_image'         => 'https://example.com/image.png',
    'og_logo'          => 'https://example.com/logo.png',    // overrides SITE_LOGO
    'og_video'         => 'https://example.com/video.mp4',
    'fb_app_id'        => '816733251417750',
    'gtm_id'           => 'GTM-XXXXXXX',
    'google_fonts_url' => 'https://fonts.googleapis.com/css2?family=Inter&display=swap',
]);
```

GA4 (`GA4_ID`), canonical URL, og:logo, and Schema.org JSON-LD are always generated automatically.

---

## Requirements

- PHP 8.4+
- Apache with `mod_rewrite`
- Composer (autoloader)
- MySQL / MariaDB (optional)

---

## Installation

1. **Clone or download** the repository into your server directory.

2. **Install Composer dependencies:**

```bash
cd TiCore
composer install
```

3. **Create and configure the environment file:**

```bash
cp TiCore/.env-example TiCore/.env
```

Then edit `TiCore/.env` and set your values:

```env
APP_ENV=production
LOG_LEVEL=1
DB_HOST=localhost
DB_NAME=your_db
DB_USER=your_user
DB_PASS=your_password
```

4. **Update site constants** in `TiCore/config.php`:

```php
define('SITE_TITLE', 'Your Site Name');
define('BASE_URL',   'https://yoursite.com');
define('GA4_ID',     'G-XXXXXXXXXX');
```

5. **Create the logs directory** and make it writable by the web server:

```bash
mkdir -p TiCore/logs
chmod 775 TiCore/logs
```

6. **Point your web server** document root to the `www/` directory. The `TiCore/` directory must never be web-accessible.

---

*TiCore Framework — Built by [Tuxxin](https://tuxxin.com) | [Live Demo](https://ticore.tuxxin.com)*
