<?php /** @var string $title @var bool $configured @var string $gsc_site @var string $ga4 @var ?array $gsc @var ?string $gscError @var ?array $sessions @var ?string $ga4Error @var ?string $authError */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,620px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:30px 28px;margin:40px 0}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 4px}.sub{color:#7c8794;font-size:.82rem;margin:0 0 20px}
 h2{color:#ffb454;font-size:.95rem;margin:24px 0 10px}
 .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px}
 .stat{background:#06070c;border:1px solid #1c2230;border-radius:10px;padding:14px}
 .stat .n{color:#e6edf3;font-size:1.5rem;font-weight:700}.stat .l{color:#7c8794;font-size:.72rem;margin-top:3px}
 .err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5;
  padding:9px 11px;border-radius:8px;font-size:.8rem;margin:10px 0}
 .note{background:rgba(255,180,84,.10);border:1px solid rgba(255,180,84,.35);color:#ffd9a3;
  padding:11px 13px;border-radius:8px;font-size:.82rem;margin:14px 0}
 .meta{color:#5c6675;font-size:.74rem;margin-top:6px}
 a{color:#ffb454;text-decoration:none}
</style></head><body>
 <div class="card">
  <h1>SEO Stats</h1><p class="sub">Last 28 days · Search Console + GA4</p>

  <?php if (!$configured): ?>
   <div class="note">Not configured. Add a Google service-account and set
    <b>GOOGLE_APPLICATION_CREDENTIALS</b>, <b>GA4_PROPERTY_ID</b>, and <b>GSC_SITE</b> in <code>.env</code>.
    See the <a href="<?= e(url('/admin/seo-stats/setup')) ?>">setup guide</a>.</div>
  <?php else: ?>
   <?php if (!empty($authError)): ?><div class="err"><?= e($authError) ?></div><?php endif; ?>

   <h2>Search Console <span class="meta"><?= e($gsc_site ?: 'no site set') ?></span></h2>
   <?php if (!empty($gsc)): ?>
    <div class="grid">
     <div class="stat"><div class="n"><?= number_format($gsc['clicks']) ?></div><div class="l">Clicks</div></div>
     <div class="stat"><div class="n"><?= number_format($gsc['impressions']) ?></div><div class="l">Impressions</div></div>
     <div class="stat"><div class="n"><?= number_format($gsc['ctr'] * 100, 2) ?>%</div><div class="l">Avg CTR</div></div>
     <div class="stat"><div class="n"><?= number_format($gsc['position'], 1) ?></div><div class="l">Avg position</div></div>
    </div>
    <p class="meta"><?= e($gsc['start']) ?> → <?= e($gsc['end']) ?></p>
   <?php elseif (!empty($gscError)): ?>
    <div class="err"><?= e($gscError) ?></div>
   <?php endif; ?>

   <h2>Google Analytics 4 <span class="meta"><?= e($ga4 ? ('property ' . $ga4) : 'no property set') ?></span></h2>
   <?php if (!empty($sessions)): ?>
    <div class="grid">
     <div class="stat"><div class="n"><?= number_format($sessions['sessions']) ?></div><div class="l">Sessions</div></div>
     <div class="stat"><div class="n"><?= number_format($sessions['totalUsers']) ?></div><div class="l">Users</div></div>
     <div class="stat"><div class="n"><?= number_format($sessions['screenPageViews']) ?></div><div class="l">Page views</div></div>
    </div>
   <?php elseif (!empty($ga4Error)): ?>
    <div class="err"><?= e($ga4Error) ?></div>
   <?php endif; ?>
  <?php endif; ?>

  <p style="margin-top:18px;font-size:.78rem;color:#5c6675">Rendered by the TiCore <b>google-analytics-gsc</b> addon ·
   <a href="<?= e(url('/admin/seo-stats/setup')) ?>">Setup</a></p>
 </div>
</body></html>
