<?php /** @var string $title @var array $types @var array $in @var ?string $jsonld @var ?string $snippet @var ?string $error */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:flex;justify-content:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 50% at 50% 0,#0e1320,#05060a)}
 .wrap{width:min(94vw,720px);padding:40px 0}
 .card{background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:28px}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 4px}.sub{color:#7c8794;font-size:.82rem;margin:0 0 20px}
 label{display:block;font-size:.78rem;color:#8b95a3;margin:14px 0 5px}
 input,select,textarea{width:100%;box-sizing:border-box;background:#06070c;border:1px solid #1c2230;border-radius:9px;
  padding:11px 12px;color:#e6edf3;font:inherit}
 input:focus,select:focus,textarea:focus{outline:none;border-color:#ff7a18}
 textarea{min-height:70px;resize:vertical}
 .hp{position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden}
 button{width:100%;margin-top:20px;background:#ff7a18;color:#1a1206;border:0;border-radius:9px;
  padding:12px;font:inherit;font-weight:700;cursor:pointer}
 .err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5;
  padding:9px 11px;border-radius:8px;font-size:.8rem;margin-bottom:14px}
 h2{color:#ffb454;font-size:.95rem;margin:26px 0 8px}
 pre{background:#06070c;border:1px solid #1c2230;border-radius:10px;padding:14px;overflow:auto;
  color:#9fe0c0;font-size:.8rem;line-height:1.5;white-space:pre;margin:0}
 .copy{background:transparent;border:1px solid #ff7a18;color:#ffb454;border-radius:8px;
  padding:7px 12px;font:inherit;font-size:.76rem;cursor:pointer;width:auto;margin:8px 0 0}
 .stateless{color:#5c6675;font-size:.74rem;margin-top:18px}
</style></head><body>
 <div class="wrap"><div class="card">
  <h1>Schema.org Generator</h1>
  <p class="sub">Build JSON-LD structured data. Stateless — nothing you enter is stored or logged.</p>

  <?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>

  <form method="post" action="<?= e(url('/tools/schema')) ?>">
   <?= csrf_field() ?>
   <div class="hp"><label>Website (leave empty)</label><input type="text" name="website" tabindex="-1" autocomplete="off" value=""></div>

   <label>Schema type</label>
   <select name="type">
    <option value="">— choose —</option>
    <?php foreach ($types as $t): ?>
     <option value="<?= e($t) ?>"<?= ($in['type'] === $t) ? ' selected' : '' ?>><?= e($t) ?></option>
    <?php endforeach; ?>
   </select>

   <label>Name</label><input type="text" name="name" value="<?= e($in['name']) ?>">
   <label>URL</label><input type="url" name="url" value="<?= e($in['url']) ?>">
   <label>Logo / Image URL</label><input type="url" name="logo" value="<?= e($in['logo']) ?>">
   <label>Description</label><textarea name="description"><?= e($in['description']) ?></textarea>
   <label>Category</label><input type="text" name="category" value="<?= e($in['category']) ?>">
   <label>sameAs (comma-separated profile URLs)</label><input type="text" name="sameAs" value="<?= e($in['sameAs']) ?>">
   <label>Contact email</label><input type="email" name="email" value="<?= e($in['email']) ?>">

   <button type="submit">Generate JSON-LD</button>
  </form>

  <?php if (!empty($jsonld)): ?>
   <h2>JSON-LD — paste in your &lt;head&gt;</h2>
   <pre id="jsonld"><?= e($jsonld) ?></pre>
   <button class="copy" type="button" onclick="navigator.clipboard.writeText(document.getElementById('jsonld').textContent)">Copy JSON-LD</button>

   <h2>PHP config snippet</h2>
   <pre id="snippet"><?= e($snippet) ?></pre>
   <button class="copy" type="button" onclick="navigator.clipboard.writeText(document.getElementById('snippet').textContent)">Copy PHP</button>
  <?php endif; ?>

  <p class="stateless">Rendered by the TiCore <b>schema-generator</b> addon · zero persistence (no DB, no file writes, no value logging).</p>
 </div></div>
</body></html>
