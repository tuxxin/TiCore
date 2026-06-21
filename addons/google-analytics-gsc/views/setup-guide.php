<?php /** @var string $title @var string $sa_json @var string $ga4_property @var string $gsc_site */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,680px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:30px 28px;margin:40px 0}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 4px}.sub{color:#7c8794;font-size:.82rem;margin:0 0 20px}
 ol{padding-left:20px;line-height:1.7;font-size:.86rem}ol b{color:#e6edf3}
 code{background:#06070c;border:1px solid #1c2230;border-radius:6px;padding:1px 6px;color:#ffb454;word-break:break-all}
 pre{background:#06070c;border:1px solid #1c2230;border-radius:10px;padding:14px;overflow:auto;
  color:#9fe0c0;font-size:.8rem;line-height:1.55}
 .row{display:flex;justify-content:space-between;gap:14px;padding:8px 0;border-bottom:1px solid #131822;font-size:.82rem;word-break:break-all}
 .row b{color:#8b95a3;font-weight:500}.row span{color:#e6edf3;text-align:right}
 a{color:#ffb454;text-decoration:none}
</style></head><body>
 <div class="card">
  <h1>SEO Stats — Setup Guide</h1>
  <p class="sub">Read-only access via a Google service-account (no OAuth dance, no SDK).</p>

  <ol>
   <li>In <b>Google Cloud Console</b> → create (or pick) a project.</li>
   <li>Enable two APIs for that project: <b>Search Console API</b> and <b>Google Analytics Data API</b>.</li>
   <li><b>IAM &amp; Admin → Service Accounts → Create</b>. Then <b>Keys → Add key → JSON</b> and download it.
       Upload that JSON to the server (outside the web root) and point <code>GOOGLE_APPLICATION_CREDENTIALS</code> at its absolute path.</li>
   <li>Copy the service-account email (looks like <code>name@project.iam.gserviceaccount.com</code>).</li>
   <li><b>Search Console</b> → your property → <b>Settings → Users and permissions → Add user</b> →
       paste the SA email, role <b>Full</b> (or Restricted). Set <code>GSC_SITE</code> to
       <code>sc-domain:example.com</code> (Domain property) or the full URL-prefix string.</li>
   <li><b>GA4</b> → <b>Admin → Property Access Management → Add</b> → paste the SA email with the
       <b>Viewer</b> role. Set <code>GA4_PROPERTY_ID</code> to the numeric property id (Admin → Property details).</li>
   <li>Add the three vars to <code>.env</code> and reload. This page reads them live:</li>
  </ol>

  <pre>GOOGLE_APPLICATION_CREDENTIALS=/home/tuxxin/.secrets/ga-gsc-sa.json
GA4_PROPERTY_ID=123456789
GSC_SITE=sc-domain:<?= e(defined('BASE_URL') ? parse_url(BASE_URL, PHP_URL_HOST) : 'example.com') ?></pre>

  <h1 style="font-size:1.05rem;margin-top:24px">Current values</h1>
  <div class="row"><b>GOOGLE_APPLICATION_CREDENTIALS</b><span><?= e($sa_json ?: '— not set —') ?></span></div>
  <div class="row"><b>GA4_PROPERTY_ID</b><span><?= e($ga4_property ?: '— not set —') ?></span></div>
  <div class="row"><b>GSC_SITE</b><span><?= e($gsc_site ?: '— not set —') ?></span></div>

  <p style="margin-top:18px;font-size:.78rem;color:#5c6675">
   Auth uses an RS256 JWT signed with the SA private key (openssl_sign), exchanged for a 1-hour
   access token — the PHP port of <code>g_token.sh</code>. ·
   <a href="<?= e(url('/admin/seo-stats')) ?>">Back to stats</a></p>
 </div>
</body></html>
