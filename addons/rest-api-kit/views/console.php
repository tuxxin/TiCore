<?php
/**
 * @var string  $title
 * @var bool    $has_key      whether a server-held console key is configured
 * @var string  $api_base
 * @var ?string $result       raw response body from the last proxied call
 * @var ?int    $status       HTTP status of the last proxied call
 * @var string  $sent_method
 * @var string  $sent_path
 *
 * The form POSTs to /admin/api-console/proxy, which forwards server-side with the
 * server-held X-Api-Key — so the key is NEVER present in the browser.
 */
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 :root{--bg:#05060a;--panel:#0c0f16;--line:#1c2230;--ink:#e6edf3;--mut:#7c8794;--mut2:#5c6675;--acc:#ff7a18;--acc2:#ffb454}
 *{box-sizing:border-box}
 body{margin:0;min-height:100vh;background:var(--bg);color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 60% at 50% 0,#0e1320,#05060a)}
 a{color:var(--acc2);text-decoration:none}
 .wrap{max-width:920px;margin:0 auto;padding:34px 22px}
 h1{color:var(--ink);font-size:1.5rem;margin:0}
 .crumb{color:var(--mut2);font-size:.8rem;margin:0 0 24px}
 .tabs{margin:0 0 22px;font-size:.85rem}.tabs a{margin-right:14px;color:var(--mut)}.tabs a.active{color:var(--acc2)}
 .panel{background:var(--panel);border:1px solid var(--line);border-radius:13px;padding:20px 22px;margin-bottom:20px}
 .panel h2{color:var(--ink);font-size:1rem;margin:0 0 14px}
 label{display:block;font-size:.78rem;color:var(--mut);margin:12px 0 6px}
 select,input[type=text],textarea{width:100%;background:#06070c;border:1px solid var(--line);border-radius:9px;
  padding:11px 12px;color:var(--ink);font:inherit}
 textarea{min-height:90px;resize:vertical}
 select:focus,input:focus,textarea:focus{outline:none;border-color:var(--acc)}
 .grid{display:grid;grid-template-columns:130px 1fr;gap:12px}
 button{margin-top:16px;background:var(--acc);color:#1a1206;border:0;border-radius:9px;padding:11px 18px;font:inherit;font-weight:700;cursor:pointer}
 .note{background:rgba(255,122,24,.08);border:1px solid rgba(255,122,24,.3);color:var(--acc2);padding:9px 12px;border-radius:9px;font-size:.78rem;margin-bottom:16px}
 .warn{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5;padding:9px 12px;border-radius:9px;font-size:.8rem;margin-bottom:16px}
 .resp .meta{color:var(--mut);font-size:.78rem;margin-bottom:8px}
 .resp .meta b{color:var(--ink)}
 .status{display:inline-block;padding:2px 9px;border-radius:999px;font-size:.72rem}
 .status.ok{background:rgba(34,197,94,.16);color:#86efac}
 .status.bad{background:rgba(239,68,68,.14);color:#fca5a5}
 pre{background:#06070c;border:1px solid var(--line);border-radius:9px;padding:14px;color:#cfe0ee;
  overflow:auto;font-size:.84rem;white-space:pre-wrap;word-break:break-word;margin:0}
</style></head><body>
 <div class="wrap">
  <h1><?= e($title) ?></h1>
  <p class="crumb">Rendered by the TiCore <b>rest-api-kit</b> addon</p>
  <div class="tabs">
   <a href="/admin">← Dashboard</a>
   <a href="/admin/api-keys">Keys</a>
   <a href="/admin/api-console" class="active">Console</a>
  </div>

  <div class="note">Requests are sent <b>server-side</b> with a server-held key. Your API key never reaches the browser.</div>
  <?php if (empty($has_key)): ?>
   <div class="warn">No server-held console key configured. Mint a key in the manager, then set <code>RESTAPIKIT_CONSOLE_KEY</code> in <code>.env</code>.</div>
  <?php endif; ?>

  <div class="panel">
   <h2>Send a request</h2>
   <form method="post" action="/admin/api-console/proxy">
    <?= csrf_field() ?>
    <div class="grid">
     <div>
      <label>Method</label>
      <select name="method">
       <option value="GET"<?= ($sent_method ?? 'GET') === 'GET' ? ' selected' : '' ?>>GET</option>
       <option value="POST"<?= ($sent_method ?? '') === 'POST' ? ' selected' : '' ?>>POST</option>
      </select>
     </div>
     <div>
      <label>Path (must start with /api/v2/)</label>
      <input type="text" name="path" value="<?= e($sent_path ?? '/api/v2/ping') ?>" placeholder="/api/v2/ping">
     </div>
    </div>
    <label>JSON body (POST only)</label>
    <textarea name="payload" placeholder='{"example":true}'></textarea>
    <button type="submit">Send via proxy</button>
   </form>
  </div>

  <?php if ($result !== null): ?>
   <div class="panel resp">
    <h2>Response</h2>
    <p class="meta">
     <b><?= e((string) ($sent_method ?? 'GET')) ?></b>
     <?= e(rtrim((string) $api_base, '/') . (string) ($sent_path ?? '')) ?>
     &nbsp;→&nbsp;
     <?php $st = (int) ($status ?? 0); ?>
     <span class="status <?= $st >= 200 && $st < 300 ? 'ok' : 'bad' ?>">HTTP <?= e((string) $st) ?></span>
    </p>
    <pre><?= e((string) $result) ?></pre>
   </div>
  <?php endif; ?>
 </div>
</body></html>
