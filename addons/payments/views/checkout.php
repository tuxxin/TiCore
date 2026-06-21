<?php /** @var array $cfg @var string $title */
$stripeOn = ($cfg['stripe_secret'] ?? '') !== '';
$paypalOn = ($cfg['paypal_client_id'] ?? '') !== '';
$cur = strtoupper($cfg['currency'] ?? 'usd');
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — TiCore</title><meta name="robots" content="noindex,nofollow">
<style>
 body{margin:0;min-height:100vh;display:grid;place-items:center;background:#05060a;color:#aeb8c4;
  font-family:ui-monospace,Menlo,Consolas,monospace;background-image:radial-gradient(120% 70% at 50% 0,#0e1320,#05060a)}
 .card{width:min(92vw,420px);background:#0c0f16;border:1px solid #1c2230;border-radius:14px;padding:28px}
 h1{color:#e6edf3;font-size:1.3rem;margin:0 0 4px}.sub{color:#7c8794;font-size:.82rem;margin:0 0 18px}
 .price{font-size:1.6rem;color:#ffb454;margin:6px 0 18px}
 button{width:100%;background:#635bff;color:#fff;border:0;border-radius:9px;padding:13px;font:inherit;font-weight:700;cursor:pointer}
 .note{background:rgba(255,180,84,.1);border:1px solid rgba(255,180,84,.35);color:#ffb454;padding:9px 11px;border-radius:8px;font-size:.78rem;margin-bottom:14px}
 .or{text-align:center;color:#5c6675;font-size:.75rem;margin:14px 0}
 #pp-result{font-size:.78rem;color:#46e08a;margin-top:10px;word-break:break-all}
</style></head><body>
 <div class="card">
  <h1>TagTrack Pro</h1><p class="sub">TiCore payments addon — Stripe + PayPal demo</p>
  <div class="price">$1.99 <span style="font-size:.8rem;color:#7c8794"><?= e($cur) ?></span></div>

  <?php if (!$stripeOn && !$paypalOn): ?>
    <div class="note">No payment provider configured. Add <b>STRIPE_SECRET_KEY</b> (test) and/or
    <b>PAYPAL_CLIENT_ID/PAYPAL_SECRET</b> to <code>.env</code>, then reload.</div>
  <?php endif; ?>

  <?php if ($stripeOn): ?>
  <form method="post" action="/pay/stripe/checkout">
    <?= csrf_field() ?>
    <button type="submit">Pay with card (Stripe) →</button>
  </form>
  <?php endif; ?>

  <?php if ($paypalOn): ?>
    <?php if ($stripeOn): ?><div class="or">— or —</div><?php endif; ?>
    <div id="paypal-buttons"></div>
    <div id="pp-result"></div>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= e($cfg['paypal_client_id']) ?>&currency=<?= e($cur) ?>"></script>
    <script>
      paypal.Buttons({
        createOrder: () => fetch('/pay/paypal/create', {method:'POST'}).then(r=>r.json()).then(d=>d.id),
        onApprove: (data) => fetch('/pay/paypal/capture', {
          method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body:'orderID='+encodeURIComponent(data.orderID)
        }).then(r=>r.json()).then(d=>{ document.getElementById('pp-result').textContent = '✓ ' + (d.status || JSON.stringify(d)); })
      }).render('#paypal-buttons');
    </script>
  <?php endif; ?>
 </div>
</body></html>
