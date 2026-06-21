<?php /** @var string $title @var bool $configured @var string $host @var string $keyLocation @var string $sitemap @var ?array $result @var ?string $error */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,560px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:30px 28px;margin:40px 0}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 4px}.sub{color:#7c8794;font-size:.82rem;margin:0 0 20px}
 .row{display:flex;justify-content:space-between;gap:14px;padding:9px 0;border-bottom:1px solid #131822;font-size:.84rem;word-break:break-all}
 .row b{color:#8b95a3;font-weight:500;flex:0 0 auto}.row span{color:#e6edf3;text-align:right}
 .badge{display:inline-block;padding:2px 9px;border-radius:999px;font-size:.72rem}
 .ok{background:rgba(34,197,94,.16);color:#86efac}.no{background:rgba(239,68,68,.16);color:#fca5a5}
 button{width:100%;margin-top:22px;background:#ff7a18;color:#1a1206;border:0;border-radius:9px;
  padding:12px;font:inherit;font-weight:700;cursor:pointer}button:disabled{opacity:.45;cursor:not-allowed}
 .err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5;
  padding:9px 11px;border-radius:8px;font-size:.8rem;margin:14px 0}
 .res{background:rgba(34,197,94,.10);border:1px solid rgba(34,197,94,.4);color:#86efac;
  padding:9px 11px;border-radius:8px;font-size:.8rem;margin:14px 0}
 a{color:#ffb454;text-decoration:none}
</style></head><body>
 <div class="card">
  <h1>IndexNow</h1><p class="sub">Push site URLs to search engines instantly.</p>

  <div class="row"><b>Key configured</b><span><?= $configured
        ? '<span class="badge ok">yes</span>'
        : '<span class="badge no">no — set INDEXNOW_KEY</span>' ?></span></div>
  <div class="row"><b>Host</b><span><?= e($host ?: '—') ?></span></div>
  <div class="row"><b>Key file</b><span><a href="<?= e($keyLocation) ?>"><?= e($keyLocation ?: '—') ?></a></span></div>
  <div class="row"><b>Sitemap</b><span><a href="<?= e($sitemap) ?>"><?= e($sitemap) ?></a></span></div>

  <?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
  <?php if (!empty($result)): ?>
   <div class="res">Submitted <?= (int) $result['count'] ?> URL(s) — IndexNow responded HTTP <?= (int) $result['code'] ?>
   <?= in_array((int) $result['code'], [200, 202], true) ? '(accepted)' : '(see response)' ?>.</div>
  <?php endif; ?>

  <form method="post" action="<?= e(url('/admin/indexnow/submit')) ?>">
   <?= csrf_field() ?>
   <button type="submit"<?= $configured ? '' : ' disabled' ?>>Fetch sitemap &amp; submit URLs</button>
  </form>
  <p style="margin-top:14px;font-size:.78rem;color:#5c6675">Rendered by the TiCore <b>indexnow</b> addon.</p>
 </div>
</body></html>
