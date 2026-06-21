<?php
namespace Tuxxin\TiCore\Addons\ContactMailer;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class ContactMailerAddon implements AddonInterface
{
    public function slug(): string { return 'contact-mailer'; }

    public function register(AddonContext $ctx): void
    {
        $ctx->views('contact-mailer');                                          // view('contact-mailer::contact')
        $ctx->config('contact-mailer', require $ctx->path . '/config/contact-mailer.php');
        $ctx->routes('routes/web.php');                                          // 'csrf' alias is provided by core
    }
}
