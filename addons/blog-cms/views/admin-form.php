<?php /** @var string $title @var ?array $post @var array $categories @var string $action @var ?string $error */ ?>
<?php
  $val = function (string $k) use ($post) { return e((string) ($post[$k] ?? '')); };
  $status = $post['status'] ?? 'draft';
  // category prefill: stored posts only carry category_id, so try category_name first.
  $catName = (string) ($post['category_name'] ?? '');
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .wrap{width:min(94vw,720px);margin:0 auto;padding:36px 0 60px}
 a{color:#ffb454;text-decoration:none}
 h1{color:#e6edf3;font-size:1.4rem;margin:0 0 18px}
 form.card{background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:26px 28px}
 label{display:block;font-size:.78rem;color:#8b95a3;margin:16px 0 5px}
 input,textarea,select{width:100%;box-sizing:border-box;background:#06070c;border:1px solid #1c2230;border-radius:9px;
  padding:11px 12px;color:#e6edf3;font:inherit}
 input:focus,textarea:focus,select:focus{outline:none;border-color:#ff7a18}
 textarea{min-height:200px;resize:vertical;line-height:1.6}
 .hint{color:#5c6675;font-size:.72rem;margin:4px 0 0}
 button{margin-top:22px;background:#ff7a18;color:#1a1206;border:0;border-radius:9px;
  padding:12px 18px;font:inherit;font-weight:700;cursor:pointer}
 .err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);color:#fca5a5;
  padding:9px 11px;border-radius:8px;font-size:.8rem;margin-bottom:14px}
 .back{font-size:.8rem;display:inline-block;margin-bottom:18px}
</style></head><body>
 <div class="wrap">
  <a class="back" href="/admin/blog">← All posts</a>
  <h1><?= e($title) ?></h1>
  <form class="card" method="post" action="<?= e($action) ?>">
   <?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>

   <label>Title</label>
   <input type="text" name="title" value="<?= $val('title') ?>" required autofocus>

   <label>Slug <span class="hint">(optional — auto-generated from title)</span></label>
   <input type="text" name="slug" value="<?= $val('slug') ?>" placeholder="my-post-slug">

   <label>Category <span class="hint">(name; created if new)</span></label>
   <input type="text" name="category" value="<?= e($catName) ?>" placeholder="e.g. Guides" list="cats">
   <datalist id="cats">
    <?php foreach ($categories as $c): ?>
     <option value="<?= e($c['name']) ?>"></option>
    <?php endforeach; ?>
   </datalist>

   <label>Excerpt</label>
   <textarea name="excerpt" style="min-height:70px"><?= $val('excerpt') ?></textarea>

   <label>Body <span class="hint">(markdown: # headings, **bold**, - lists)</span></label>
   <textarea name="body"><?= $val('body') ?></textarea>

   <label>Status</label>
   <select name="status">
    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
    <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
   </select>

   <?= csrf_field() ?>
   <button type="submit">Save post</button>
  </form>
 </div>
</body></html>
