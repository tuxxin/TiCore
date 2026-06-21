<?php
namespace Tuxxin\TiCore\Addons\SchemaGenerator;

use TiCore\Core\AddonInterface;
use TiCore\Core\AddonContext;

final class SchemaGeneratorAddon implements AddonInterface
{
    public function slug(): string { return 'schema-generator'; }

    public function register(AddonContext $ctx): void
    {
        $ctx->views('schema-generator');
        $ctx->config('schema-generator', require $ctx->path . '/config/schema-generator.php');
        $ctx->routes('routes/web.php');
    }
}
