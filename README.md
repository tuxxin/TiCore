# TiCore — a security-first, SEO-first PHP framework (v2)

TiCore is a lightweight PHP 8.1+ MVC micro-framework: a modern router, a drop-in
addon ecosystem, built-in security, and a complete SEO/structured-data suite —
with near-zero dependencies and a public/private directory split that keeps
sensitive files out of the web root.

## Highlights (v2)

- **Modern router** — parameterized routes (`/post/{id:\d+}`), unlimited-depth
  catch-alls (`/docs/{path:.*}`), HTTP-method routing, **named routes** with reverse
  URL generation, route **groups**, and a **middleware** pipeline. Backward-compatible
  zero-config controller/view auto-discovery remains as a fallback.
- **Addon ecosystem** — drop-in features installed with the `ticore` CLI, pulled
  separately from the base: `auth`, `payments`, `blog-cms`, `contact-mailer`,
  `admin-dashboard`, `rest-api-kit`, `indexnow`, `google-analytics-gsc`,
  `google-ecommerce`, `schema-generator`.
- **Security** — CSRF middleware, hardened sessions (HttpOnly/Secure/SameSite +
  regeneration), security headers, traversal-guarded routing, PDO prepared
  statements, secrets in `.env` outside the web root.
- **SEO** — canonical, Open Graph, Twitter cards, a config-driven **JSON-LD
  SchemaBuilder** (Organization/WebSite/WebPage + Product/Article/FAQ/…), and an
  auto-generating sitemap.

## Project layout

```
TiCore/            framework + your app (NOT web-accessible)
  config.php       settings (identity + secrets via .env)
  routes/          web.php, api.php
  src/Core/        Router, Route, Http/, Middleware/, Seo/, Security, Logger, …
  src/Controllers/
  templates/default/
  addons/          installed addons land here (gitignored)
  database/        sqlite (gitignored)
www/               public web root: index.php, .htaccess, assets
addons/            addon SOURCES (this repo) — installed via the CLI
bin/ticore         CLI
```

## Quick start

```bash
git clone https://github.com/tuxxin/TiCore.git
cd TiCore
cp TiCore/.env-example TiCore/.env   # set SITE_TITLE, BASE_URL, …
# point your web server's docroot at www/  (Apache mod_rewrite or Nginx)
```

Drop a view in `TiCore/templates/default/` or a controller in
`TiCore/src/Controllers/` and it serves with zero config. Declare explicit routes
in `TiCore/routes/web.php`.

## Routing

```php
$router->get('/blog/{slug}', 'BlogController@show')->name('blog.show');
$router->get('/post/{id:\d+}', fn(Request $r) => Response::json(['id' => (int)$r->param('id')]));
$router->get('/docs/{path:.*}', 'DocsController@show');          // any depth
$router->post('/contact', 'ContactController@submit', ['csrf']); // method + middleware
$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) {
    $router->get('/dashboard', 'Admin\DashboardController@index');
});
echo route('blog.show', ['slug' => 'hello']);                    // reverse URL
```

## Addons & the CLI

```bash
bin/ticore addon list
bin/ticore addon add auth        # full user accounts
bin/ticore addon add payments    # Stripe + PayPal
bin/ticore migrate
bin/ticore key:generate
bin/ticore secrets:scan          # pre-commit secret check
```

Addons live in `/addons/<slug>/` and are excluded from base release tarballs
(`.gitattributes export-ignore`); the CLI fetches them on demand.

## Security

CSRF (`csrf_field()` + the `csrf` middleware), hardened sessions, security headers,
strict route validation + `realpath` traversal guard, PDO prepared statements, and
`.env` outside the web root. A `secrets:scan` CLI command and a gitleaks CI workflow
keep credentials out of commits.

## License

MIT © Tuxxin.
