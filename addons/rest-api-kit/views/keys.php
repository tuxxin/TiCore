<?php
/**
 * @var string  $title
 * @var array   $keys     [{id,name,key_prefix,active,created_at,last_used_at}]
 * @var ?string $created  full plaintext key shown ONCE right after creation
 * @var ?string $error
 *
 * Matches the admin-dashboard dark shell so admin pages look consistent.
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
 .top{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:6px}
 h1{color:var(--ink);font-size:1.5rem;margin:0}
 .crumb{color:var(--mut2);font-size:.8rem;margin:0 0 24px}
 .tabs{margin:0 0 22px;font-size:.85rem}.tabs a{margin-right:14px;color:var(--mut)}.tabs a.active{color:var(--acc2)}
 .panel{background:var(--panel);border:1px solid var(--line);border-radius:13px;padding:20px 22px;margin-bottom:20px}
 .panel h2{color:var(--ink);font-size:1rem;margin:0 0 14px}
 label{display:block;font-size:.78rem;color:var(--mut);margin:0 0 6px}
 .row{display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap}
 input[type=text]{flex:1;min-width:200px;background:#06070c;border:1px solid var(--line);border-radius:9px;
  padding:11px 12px;color:var(--ink);font:inherit}
 input:focus{outline:none;border-color:var(--acc)}
 button{background:var(--acc);color:#1a1206;border:0;border-radius:9px;padding:11px 18px;font:inherit;font-weight:700;cursor:pointer}
 button.ghost{background:transparent;border:1px solid #5a2a14;color:#f2a07a;padding:7px 12px;font-weight:500}
 button.ghost:hover{border-color:#ef4444;color:#fca5a5}
 .reveal{background:rgba(255,122,24,.1);border:1px solid rgba(255,122,24,.4);border-radius:10px;padding:14px 16px;margin-bottom:18px}
 .reveal .k{color:var(--acc2);font-size:.74rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px}
 .reveal code{display:block;background:#06070c;border:1px solid var(--line);border-radius:8px;padding:11px 12px;color:#e6edf3;word-break:break-all;font-size:.9rem}
 .reveal .warn{color:#f2a07a;font-size:.76rem;margin-top:8px}
 .err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5;padding:9px 11px;border-radius:8px;font-size:.8rem;margin-bottom:14px}
 table{width:100%;border-collapse:collapse;font-size:.84rem}
 th,td{text-align:left;padding:10px 8px;border-bottom:1px solid #131822;vertical-align:middle}
 th{color:var(--mut);font-weight:500;font-size:.74rem;text-transform:uppercase;letter-spacing:.04em}
 td .name{color:var(--ink);font-weight:600}
 .pill{display:inline-block;padding:2px 9px;border-radius:999px;font-size:.72rem}
 .pill.on{background:rgba(34,197,94,.16);color:#86efac}
 .pill.off{background:rgba(239,68,68,.14);color:#fca5a5}
 .muted{color:var(--mut2)}
 .empty{color:var(--mut);font-size:.86rem}
</style></head><body>
 <div class="wrap">
  <div class="top"><h1><?= e($title) ?></h1></div>
  <p class="crumb">Rendered by the TiCore <b>rest-api-kit</b> addon</p>
  <div class="tabs">
   <a href="/admin">← Dashboard</a>
   <a href="/admin/api-keys" class="active">Keys</a>
   <a href="/admin/api-console">Console</a>
  </div>

  <?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>

  <?php if (!empty($created)): ?>
   <div class="reveal">
    <div class="k">New API key — copy it now</div>
    <code><?= e($created) ?></code>
    <p class="warn">This is the only time the full key is shown. Only its hash is stored — if you lose it, revoke and mint a new one.</p>
   </div>
  <?php endif; ?>

  <div class="panel">
   <h2>Create a key</h2>
   <form method="post" action="/admin/api-keys">
    <?= csrf_field() ?>
    <div class="row">
     <div style="flex:1">
      <label>Name / label</label>
      <input type="text" name="name" placeholder="e.g. mobile-app" maxlength="120" autofocus>
     </div>
     <button type="submit">Generate key</button>
    </div>
   </form>
  </div>

  <div class="panel">
   <h2>Keys</h2>
   <?php if ($keys): ?>
    <table>
     <thead><tr><th>Name</th><th>Prefix</th><th>Status</th><th>Created</th><th>Last used</th><th></th></tr></thead>
     <tbody>
      <?php foreach ($keys as $k): ?>
       <tr>
        <td><span class="name"><?= e((string) $k['name']) ?></span></td>
        <td><code><?= e((string) $k['key_prefix']) ?>…</code></td>
        <td>
         <?php if ((int) $k['active'] === 1): ?>
          <span class="pill on">active</span>
         <?php else: ?>
          <span class="pill off">revoked</span>
         <?php endif; ?>
        </td>
        <td class="muted"><?= e((string) ($k['created_at'] ?? '—')) ?></td>
        <td class="muted"><?= e((string) ($k['last_used_at'] ?? 'never')) ?></td>
        <td>
         <?php if ((int) $k['active'] === 1): ?>
          <form method="post" action="/admin/api-keys/revoke/<?= (int) $k['id'] ?>" onsubmit="return confirm('Revoke this key?')">
           <?= csrf_field() ?>
           <button type="submit" class="ghost">Revoke</button>
          </form>
         <?php else: ?>
          <span class="muted">—</span>
         <?php endif; ?>
        </td>
       </tr>
      <?php endforeach; ?>
     </tbody>
    </table>
   <?php else: ?>
    <p class="empty">No API keys yet. Generate one above.</p>
   <?php endif; ?>
  </div>
 </div>
</body></html>
