<?php
// TiCore/src/Core/AddonInterface.php
namespace TiCore\Core;

/**
 * A drop-in addon. Its register() runs once per request after the Router is
 * built and before dispatch — it wires routes, middleware, views, and config.
 */
interface AddonInterface
{
    public function slug(): string;
    public function register(AddonContext $ctx): void;
}
