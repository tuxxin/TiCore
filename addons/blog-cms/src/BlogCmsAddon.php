<?php
namespace Tuxxin\TiCore\Addons\BlogCms;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class BlogCmsAddon implements AddonInterface
{
    public function slug(): string { return 'blog-cms'; }

    public function register(AddonContext $ctx): void
    {
        if (!defined('BLOGCMS_DB_PATH')) {
            define('BLOGCMS_DB_PATH', getenv('BLOGCMS_DB_PATH') ?: (CORE_PATH . '/database/blog-cms.sqlite'));
        }
        $ctx->views('blog-cms');                                       // view('blog-cms::list') etc.
        $ctx->config('blog-cms', require $ctx->path . '/config/blog-cms.php');
        $ctx->routes('routes/web.php');                                 // 'auth'/'csrf' aliases come from core
    }
}
