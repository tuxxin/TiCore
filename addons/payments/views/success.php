<?php /** @var array $cfg @var ?array $session @var string $title */
$paid = $session && (($session['payment_status'] ?? '') === 'paid' || ($session['status'] ?? '') === 'complete');
$amount = $session ? number_format(((int)($session['amount_total'] ?? 0)) / 100, 2) : null;
$cur = strtoupper($session['currency'] ?? ($cfg['currency'] ?? 'usd'));
$ga4 = $cfg['ga4_id'] ?? '';
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,420px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:30px;text-align:center}
 h1{color:#46e08a;font-size:1.4rem;margin:0 0 8px}
 .amt{color:#ffb454;font-size:1.6rem;margin:10px 0}
 a{color:#ffb454;text-decoration:none}
</style>
<?php if ($paid && $ga4): /* GA4 purchase conversion event (the analytics-tag integration) */ ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga4) ?>"></script>
<script>
 window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
 gtag('js',new Date());gtag('config','<?= e($ga4) ?>');
 gtag('event','purchase',{transaction_id:'<?= e($session['id'] ?? '') ?>',value:<?= json_encode((float)$amount) ?>,currency:'<?= e($cur) ?>'});
</script>
<?php endif; ?>
</head><body>
 <div class="card">
  <?php if ($paid): ?>
   <h1>✅ Payment received</h1>
   <?php if ($amount): ?><div class="amt">$<?= e($amount) ?> <?= e($cur) ?></div><?php endif; ?>
   <p>Thank you! Your TagTrack Pro demo payment is complete.</p>
  <?php else: ?>
   <h1 style="color:#ffb454">Order status</h1>
   <p><?= $session ? 'Payment not completed.' : 'No session to confirm (open this page after a Stripe checkout).' ?></p>
  <?php endif; ?>
  <p style="margin-top:16px;font-size:.8rem"><a href="/pay">&larr; back to checkout</a></p>
 </div>
</body></html>
