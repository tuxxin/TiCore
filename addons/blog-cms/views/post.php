<?php /** @var string $title @var ?array $post @var string $html */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title>
<?php if (empty($post)): ?><meta name="robots" content="noindex,nofollow"><?php endif; ?>
<style>
 body{margin:0;min-height:100vh;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .wrap{width:min(92vw,720px);margin:0 auto;padding:40px 0 60px}
 a{color:#ffb454;text-decoration:none}a:hover{color:#ff7a18}
 .back{font-size:.8rem;display:inline-block;margin-bottom:22px}
 article{background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:28px 30px}
 h1{color:#e6edf3;font-size:1.6rem;margin:0 0 6px}
 .meta{color:#5c6675;font-size:.76rem;margin:0 0 22px}
 .badge{display:inline-block;background:rgba(255,122,24,.16);color:#ffb454;padding:1px 8px;border-radius:999px;font-size:.68rem}
 .body{color:#cdd6df;font-size:.95rem;line-height:1.75}
 .body h1,.body h2,.body h3{color:#e6edf3}
 .body strong{color:#ffd9a8}
 .body ul{padding-left:20px}.body li{margin:4px 0}
 .nf{color:#fca5a5}
</style></head><body>
 <div class="wrap">
  <a class="back" href="/blog">← Back to blog</a>
  <?php if (empty($post)): ?>
   <article><h1>Post not found</h1><p class="nf">That post doesn’t exist or isn’t published.</p></article>
  <?php else: ?>
   <article>
    <h1><?= e($post['title']) ?></h1>
    <p class="meta">
     <?= e($post['published_at'] ?? $post['updated_at'] ?? '') ?>
     <?php if (!empty($post['category_name'])): ?>
      · <a href="/blog/category/<?= e($post['category_slug']) ?>"><span class="badge"><?= e($post['category_name']) ?></span></a>
     <?php endif; ?>
    </p>
    <div class="body"><?= $html /* already escaped + sanitized by Blog::mdToHtml */ ?></div>
   </article>
  <?php endif; ?>
 </div>
</body></html>
