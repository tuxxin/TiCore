<?php
// TiCore/src/Core/AddonContext.php
namespace TiCore\Core;

/** The surface an addon touches at boot. Passed to AddonInterface::register(). */
final class AddonContext
{
    public function __construct(
        public Router $router,
        public string $path,       // absolute path to the addon directory
        public string $slug,
        public string $assetUrl    // public asset base, e.g. /assets/addons/<slug>
    ) {}

    /** Register the addon's views/ directory under a view namespace (default = slug). */
    public function views(?string $ns = null): void
    {
        view_namespace($ns ?? $this->slug, $this->path . '/views');
    }

    /** Register a named middleware the addon provides. */
    public function middleware(string $alias, string $class): void
    {
        $this->router->aliasMiddleware($alias, $class);
    }

    /** Load one of the addon's route files (with $router in scope). */
    public function routes(string $relFile): void
    {
        $file = $this->path . '/' . ltrim($relFile, '/');
        if (is_file($file)) $this->router->loadRoutes($file);
    }

    /** Merge a config value addons + sites can read via config(). */
    public function config(string $key, $value): void { config_set($key, $value); }
}
