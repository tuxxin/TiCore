<?php
namespace Tuxxin\TiCore\Addons\Payments;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class PaymentsAddon implements AddonInterface
{
    public function slug(): string { return 'payments'; }

    public function register(AddonContext $ctx): void
    {
        $ctx->views('payments');
        $ctx->config('payments', require $ctx->path . '/config/payments.php');
        $ctx->routes('routes/web.php');
    }
}
