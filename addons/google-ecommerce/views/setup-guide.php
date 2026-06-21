<?php /** @var string $title @var string $feedUrl @var string $currency @var string $feedSource @var int $productCount */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,680px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:30px 28px;margin:40px 0}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 4px}.sub{color:#7c8794;font-size:.82rem;margin:0 0 20px}
 h2{color:#ffb454;font-size:.95rem;margin:24px 0 10px}
 ol{padding-left:20px;line-height:1.7;font-size:.86rem}ol b{color:#e6edf3}
 code{background:#06070c;border:1px solid #1c2230;border-radius:6px;padding:1px 6px;color:#ffb454;word-break:break-all}
 .row{display:flex;justify-content:space-between;gap:14px;padding:8px 0;border-bottom:1px solid #131822;font-size:.82rem;word-break:break-all}
 .row b{color:#8b95a3;font-weight:500}.row span{color:#e6edf3;text-align:right}
 .note{background:rgba(255,180,84,.10);border:1px solid rgba(255,180,84,.35);color:#ffd9a3;
  padding:11px 13px;border-radius:8px;font-size:.82rem;margin:14px 0}
 .btn{display:inline-block;margin-top:8px;background:#ff7a18;color:#1a1206;border-radius:9px;
  padding:10px 16px;font-weight:700;text-decoration:none}
 a{color:#ffb454;text-decoration:none}
</style></head><body>
 <div class="card">
  <h1>Google Ecommerce</h1>
  <p class="sub">Merchant Center product feed + Business Profile linkage.</p>

  <div class="row"><b>Feed URL</b><span><a href="<?= e($feedUrl) ?>"><?= e($feedUrl) ?></a></span></div>
  <div class="row"><b>Currency</b><span><?= e($currency) ?></span></div>
  <div class="row"><b>Items in feed</b><span><?= (int) $productCount ?></span></div>
  <a class="btn" href="<?= e($feedUrl) ?>">View feed XML →</a>

  <?php if (!empty($feedSource)): ?>
   <div class="note"><b>Data source:</b> <?= e($feedSource) ?></div>
  <?php endif; ?>

  <h2>1 · Submit the feed to Merchant Center</h2>
  <ol>
   <li>Create / open <b>Google Merchant Center</b> and verify + claim your website URL.</li>
   <li><b>Products → Feeds → Add primary feed</b>. Choose <b>Scheduled fetch</b>.</li>
   <li>Paste the feed URL above (<code><?= e($feedUrl) ?></code>) and pick a daily fetch time.</li>
   <li>Fix any item-level disapprovals Merchant Center reports (GTIN/brand, shipping, returns policy).</li>
  </ol>

  <h2>2 · Replace the sample products</h2>
  <ol>
   <li>Edit <code>config/google-ecommerce.php</code> → set the <code>products</code> key to your real catalog
       (array, a closure that returns rows, or a DB query).</li>
   <li>Each row needs at least: <code>id, title, description, link, image_link, availability, price, condition</code>.
       Add <code>brand/gtin/mpn/google_product_category</code> where you have them.</li>
  </ol>

  <h2>3 · Business Profile linkage (free local listings)</h2>
  <ol>
   <li>In Merchant Center → <b>Growth / Manage programs</b> → enable <b>Free listings</b> and <b>Local inventory</b> (if applicable).</li>
   <li>Link your <b>Google Business Profile</b> under <b>Settings → Linked accounts</b> so products can surface on Maps / Search.</li>
  </ol>

  <p style="margin-top:18px;font-size:.78rem;color:#5c6675">Rendered by the TiCore <b>google-ecommerce</b> addon.</p>
 </div>
</body></html>
