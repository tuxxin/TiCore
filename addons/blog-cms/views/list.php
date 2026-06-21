<?php /** @var string $title @var string $heading @var array $posts @var array $categories @var ?array $category */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title>
<style>
 body{margin:0;min-height:100vh;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .wrap{width:min(92vw,760px);margin:0 auto;padding:40px 0 60px}
 h1{color:#e6edf3;font-size:1.6rem;margin:0 0 6px}
 .sub{color:#7c8794;font-size:.82rem;margin:0 0 24px}
 .cats{margin:0 0 26px;font-size:.8rem}.cats a{margin-right:12px}
 a{color:#ffb454;text-decoration:none}a:hover{color:#ff7a18}
 .post{background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:20px 22px;margin-bottom:16px}
 .post h2{margin:0 0 6px;font-size:1.15rem}.post h2 a{color:#e6edf3}
 .meta{color:#5c6675;font-size:.74rem;margin:0 0 10px}
 .excerpt{color:#aeb8c4;font-size:.9rem;line-height:1.6;margin:0}
 .badge{display:inline-block;background:rgba(255,122,24,.16);color:#ffb454;padding:1px 8px;border-radius:999px;font-size:.68rem}
 .empty{color:#5c6675;font-size:.9rem}
</style></head><body>
 <div class="wrap">
  <h1><?= e($heading) ?></h1>
  <p class="sub">TiCore blog-cms addon</p>

  <?php if (!empty($categories)): ?>
   <nav class="cats">
    <a href="/blog">All</a>
    <?php foreach ($categories as $c): ?>
     <a href="/blog/category/<?= e($c['slug']) ?>"><?= e($c['name']) ?></a>
    <?php endforeach; ?>
   </nav>
  <?php endif; ?>

  <?php if (empty($posts)): ?>
   <p class="empty">No posts yet.</p>
  <?php else: ?>
   <?php foreach ($posts as $p): ?>
    <article class="post">
     <h2><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h2>
     <p class="meta">
      <?= e($p['published_at'] ?? $p['updated_at'] ?? '') ?>
      <?php if (!empty($p['category_name'])): ?>
       · <span class="badge"><?= e($p['category_name']) ?></span>
      <?php endif; ?>
     </p>
     <?php if (!empty($p['excerpt'])): ?>
      <p class="excerpt"><?= e($p['excerpt']) ?></p>
     <?php endif; ?>
    </article>
   <?php endforeach; ?>
  <?php endif; ?>
 </div>
</body></html>
