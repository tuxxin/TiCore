<?php /** @var string $title @var ?array $user */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,440px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:30px 28px}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 18px}
 .row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #131822;font-size:.88rem}
 .row b{color:#8b95a3;font-weight:500}.row span{color:#e6edf3}
 .badge{display:inline-block;background:rgba(255,122,24,.16);color:#ffb454;padding:2px 9px;border-radius:999px;font-size:.72rem}
 button{margin-top:22px;background:transparent;border:1px solid #ff7a18;color:#ffb454;border-radius:9px;
  padding:10px 16px;font:inherit;cursor:pointer}
 a{color:#ffb454;text-decoration:none}
</style></head><body>
 <div class="card">
  <h1>✅ Signed in</h1>
  <?php if ($user): ?>
   <div class="row"><b>Email</b><span><?= e($user['email']) ?></span></div>
   <div class="row"><b>Name</b><span><?= e($user['name'] ?? '—') ?></span></div>
   <div class="row"><b>Role</b><span class="badge"><?= e($user['role']) ?></span></div>
   <div class="row"><b>Joined</b><span><?= e($user['created_at']) ?></span></div>
  <?php else: ?>
   <p>Not signed in.</p>
  <?php endif; ?>
  <form method="post" action="/logout">
   <?= csrf_field() ?>
   <button type="submit">Log out</button>
  </form>
  <p style="margin-top:14px;font-size:.78rem;color:#5c6675">Rendered by the TiCore <b>auth</b> addon.</p>
 </div>
</body></html>
