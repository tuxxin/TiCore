<?php /** @var string $title @var array $posts */ ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .wrap{width:min(94vw,860px);margin:0 auto;padding:36px 0 60px}
 .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px}
 h1{color:#e6edf3;font-size:1.4rem;margin:0}
 a{color:#ffb454;text-decoration:none}a:hover{color:#ff7a18}
 .btn{background:#ff7a18;color:#1a1206;border:0;border-radius:9px;padding:9px 14px;font:inherit;font-weight:700;cursor:pointer}
 table{width:100%;border-collapse:collapse;background:#0c0f16;border:1px solid #1c2230;border-radius:14px;overflow:hidden}
 th,td{text-align:left;padding:11px 14px;font-size:.84rem;border-bottom:1px solid #131822}
 th{color:#8b95a3;font-weight:500;background:#0a0d14}
 td .badge{display:inline-block;padding:1px 8px;border-radius:999px;font-size:.68rem}
 .s-published{background:rgba(34,197,94,.16);color:#86efac}
 .s-draft{background:rgba(255,122,24,.16);color:#ffb454}
 .actions form{display:inline}
 .actions button.link{background:none;border:0;color:#fca5a5;font:inherit;cursor:pointer;padding:0}
 .empty{color:#5c6675;font-size:.9rem;padding:16px}
</style></head><body>
 <div class="wrap">
  <div class="head">
   <h1>Blog admin</h1>
   <a class="btn" href="/admin/blog/new">+ New post</a>
  </div>
  <table>
   <tr><th>ID</th><th>Title</th><th>Category</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
   <?php if (empty($posts)): ?>
    <tr><td colspan="6" class="empty">No posts yet — create one.</td></tr>
   <?php else: ?>
    <?php foreach ($posts as $p): ?>
     <tr>
      <td><?= (int) $p['id'] ?></td>
      <td><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></td>
      <td><?= e($p['category_name'] ?? '—') ?></td>
      <td><span class="badge s-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
      <td><?= e($p['updated_at'] ?? '') ?></td>
      <td class="actions">
       <a href="/admin/blog/edit/<?= (int) $p['id'] ?>">Edit</a> ·
       <form method="post" action="/admin/blog/delete/<?= (int) $p['id'] ?>" onsubmit="return confirm('Delete this post?')">
        <?= csrf_field() ?>
        <button type="submit" class="link">Delete</button>
       </form>
      </td>
     </tr>
    <?php endforeach; ?>
   <?php endif; ?>
  </table>
 </div>
</body></html>
