<?php /** @var string $title @var ?string $error @var array $old */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title>
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,440px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:30px 28px}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 4px}.sub{color:#7c8794;font-size:.82rem;margin:0 0 20px}
 label{display:block;font-size:.78rem;color:#8b95a3;margin:14px 0 5px}
 input,textarea{width:100%;box-sizing:border-box;background:#06070c;border:1px solid #1c2230;border-radius:9px;
  padding:11px 12px;color:#e6edf3;font:inherit}
 input:focus,textarea:focus{outline:none;border-color:#ff7a18}
 textarea{min-height:130px;resize:vertical;line-height:1.6}
 button{width:100%;margin-top:20px;background:#ff7a18;color:#1a1206;border:0;border-radius:9px;
  padding:12px;font:inherit;font-weight:700;cursor:pointer}
 .err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5;
  padding:9px 11px;border-radius:8px;font-size:.8rem;margin-bottom:14px}
 a{color:#ffb454;text-decoration:none}
 /* honeypot — visually hidden, off-screen, not announced to screen readers */
 .hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}
</style></head><body>
 <form class="card" method="post" action="/contact/submit">
  <h1>Get in touch</h1><p class="sub">TiCore contact-mailer addon</p>
  <?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>

  <label>Name</label>
  <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>" autocomplete="name">

  <label>Email</label>
  <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="email">

  <label>Message</label>
  <textarea name="message" required><?= e($old['message'] ?? '') ?></textarea>

  <!-- honeypot: real users never see/fill this -->
  <div class="hp" aria-hidden="true">
   <label>Website</label>
   <input type="text" name="website" tabindex="-1" autocomplete="off">
  </div>

  <?= csrf_field() ?>
  <button type="submit">Send message</button>
 </form>
</body></html>
