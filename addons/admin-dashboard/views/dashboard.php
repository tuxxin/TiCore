<?php
/**
 * @var string  $title
 * @var array   $cfg     admin-dashboard config (brand, nav, logout_url)
 * @var ?string $email   logged-in user email (from session)
 * @var ?string $role    logged-in user role (from session)
 * @var string  $php     PHP version
 * @var array   $addons  installed addons [{slug,name,version,description}]
 *
 * Reusable dark admin shell. Other admin pages can copy this <head>/<style> +
 * .shell/.sidebar/.main structure (or render inside it) to match the look.
 */
$brand  = $cfg['brand'] ?? 'TiCore Admin';
$nav    = $cfg['nav'] ?? ['Dashboard' => '/admin'];
$logout = $cfg['logout_url'] ?? '/logout';
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 :root{--bg:#05060a;--panel:#0c0f16;--line:#1c2230;--ink:#e6edf3;--mut:#7c8794;--mut2:#5c6675;--acc:#ff7a18;--acc2:#ffb454}
 *{box-sizing:border-box}
 body{margin:0;min-height:100vh;background:var(--bg);color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;
  background-image:radial-gradient(120% 60% at 50% 0,#0e1320,#05060a)}
 a{color:var(--acc2);text-decoration:none}
 .shell{display:grid;grid-template-columns:230px 1fr;min-height:100vh}
 .sidebar{background:#080a10;border-right:1px solid var(--line);padding:22px 16px;display:flex;flex-direction:column}
 .brand{color:var(--ink);font-size:1.05rem;font-weight:700;margin:0 0 22px;display:flex;align-items:center;gap:8px}
 .brand .dot{width:9px;height:9px;border-radius:50%;background:var(--acc);box-shadow:0 0 10px var(--acc)}
 .nav a{display:block;padding:9px 11px;border-radius:9px;color:var(--mut);font-size:.86rem;margin-bottom:2px}
 .nav a:hover{background:#0e1320;color:var(--ink)}
 .nav a.active{background:rgba(255,122,24,.14);color:var(--acc2)}
 .sidebar form{margin-top:auto}
 .logout{width:100%;background:transparent;border:1px solid var(--line);color:var(--mut);border-radius:9px;
  padding:9px;font:inherit;font-size:.82rem;cursor:pointer;margin-top:18px}
 .logout:hover{border-color:var(--acc);color:var(--acc2)}
 .main{padding:30px 34px;max-width:1100px}
 h1{color:var(--ink);font-size:1.5rem;margin:0 0 2px}
 .crumb{color:var(--mut2);font-size:.8rem;margin:0 0 26px}
 .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:28px}
 .card{background:var(--panel);border:1px solid var(--line);border-radius:13px;padding:16px 18px}
 .card .k{color:var(--mut);font-size:.74rem;text-transform:uppercase;letter-spacing:.04em}
 .card .v{color:var(--ink);font-size:1.05rem;margin-top:6px;word-break:break-all}
 .badge{display:inline-block;background:rgba(255,122,24,.16);color:var(--acc2);padding:2px 9px;border-radius:999px;font-size:.72rem}
 .section{background:var(--panel);border:1px solid var(--line);border-radius:13px;padding:20px 22px}
 .section h2{color:var(--ink);font-size:1rem;margin:0 0 14px;display:flex;justify-content:space-between;align-items:baseline}
 .section h2 small{color:var(--mut2);font-weight:400;font-size:.76rem}
 table{width:100%;border-collapse:collapse;font-size:.84rem}
 th,td{text-align:left;padding:10px 8px;border-bottom:1px solid #131822;vertical-align:top}
 th{color:var(--mut);font-weight:500;font-size:.74rem;text-transform:uppercase;letter-spacing:.04em}
 td .slug{color:var(--ink);font-weight:600}
 td .ver{color:var(--acc2)}
 td .desc{color:var(--mut)}
 .foot{color:var(--mut2);font-size:.76rem;margin-top:22px}
</style></head><body>
 <div class="shell">
  <aside class="sidebar">
   <p class="brand"><span class="dot"></span><?= e($brand) ?></p>
   <nav class="nav">
    <?php foreach ($nav as $label => $path): ?>
     <a href="<?= e($path) ?>"<?= $path === '/admin' ? ' class="active"' : '' ?>><?= e((string) $label) ?></a>
    <?php endforeach; ?>
   </nav>
   <form method="post" action="<?= e($logout) ?>">
    <?= csrf_field() ?>
    <button type="submit" class="logout">Log out</button>
   </form>
  </aside>

  <main class="main">
   <h1><?= e($title) ?></h1>
   <p class="crumb">Rendered by the TiCore <b>admin-dashboard</b> addon</p>

   <div class="cards">
    <div class="card">
     <div class="k">Signed in as</div>
     <div class="v"><?= e($email ?? 'unknown') ?></div>
    </div>
    <div class="card">
     <div class="k">Role</div>
     <div class="v"><span class="badge"><?= e($role ?? 'user') ?></span></div>
    </div>
    <div class="card">
     <div class="k">PHP version</div>
     <div class="v"><?= e($php) ?></div>
    </div>
    <div class="card">
     <div class="k">Installed addons</div>
     <div class="v"><?= count($addons) ?></div>
    </div>
   </div>

   <section class="section">
    <h2>Installed addons <small><?= count($addons) ?> discovered</small></h2>
    <?php if ($addons): ?>
     <table>
      <thead><tr><th>Slug</th><th>Version</th><th>Description</th></tr></thead>
      <tbody>
       <?php foreach ($addons as $a): ?>
        <tr>
         <td><span class="slug"><?= e($a['slug']) ?></span></td>
         <td><span class="ver"><?= e($a['version']) ?></span></td>
         <td><span class="desc"><?= e($a['description']) ?></span></td>
        </tr>
       <?php endforeach; ?>
      </tbody>
     </table>
    <?php else: ?>
     <p>No addons found under <code>CORE_PATH/addons</code>.</p>
    <?php endif; ?>
   </section>

   <p class="foot">TiCore v2 · reusable admin shell — other admin pages can match this <code>.shell</code> layout.</p>
  </main>
 </div>
</body></html>
