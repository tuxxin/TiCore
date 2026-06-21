<?php
// TiCore/src/Core/AddonManager.php
namespace TiCore\Core;

/**
 * Discovers installed addons under TiCore/addons/<slug>/, registers a PSR-4
 * autoloader for their namespaces, resolves dependency order, and boots each
 * (calls register()). No-op if the addons dir is absent — existing sites and
 * the bare base are unaffected.
 */
final class AddonManager
{
    /** @var array<string,array> slug => manifest (+ __path) */
    private array $manifests = [];

    public function __construct(private string $dir) {}

    public function boot(Router $router): void
    {
        if (!is_dir($this->dir)) return;

        foreach (glob($this->dir . '/*/addon.json') as $mf) {
            $m = json_decode((string) @file_get_contents($mf), true);
            if (!is_array($m) || empty($m['slug'])) continue;
            $slug = basename(dirname($mf));
            // slug must match dir + charset (mirrors Router segment validation)
            if ($m['slug'] !== $slug || !preg_match('/^[a-z0-9][a-z0-9\-]*$/', $slug)) {
                Logger::error("Addon manifest slug mismatch/invalid: $slug");
                continue;
            }
            $m['__path'] = dirname($mf);
            $this->manifests[$slug] = $m;
        }
        if (!$this->manifests) return;

        // PSR-4 autoloader for addon namespaces (base keeps manual requires).
        spl_autoload_register(function (string $class): void {
            foreach ($this->manifests as $m) {
                foreach (($m['autoload'] ?? []) as $prefix => $rel) {
                    if (str_starts_with($class, $prefix)) {
                        $f = $m['__path'] . '/' . rtrim($rel, '/') . '/'
                           . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
                        if (is_file($f)) { require $f; return; }
                    }
                }
            }
        });

        foreach ($this->order() as $slug) {
            $m = $this->manifests[$slug];
            $class = $m['bootstrap'] ?? null;
            if (!$class || !class_exists($class)) {
                Logger::error("Addon '$slug': bootstrap class missing: " . (string) $class);
                continue;
            }
            $addon = new $class();
            if (!$addon instanceof AddonInterface) {
                Logger::error("Addon '$slug': bootstrap is not an AddonInterface");
                continue;
            }
            $ctx = new AddonContext($router, $m['__path'], $slug, '/assets/addons/' . $slug);
            $addon->register($ctx);
        }
    }

    /** Dependency order: an addon's `requires` boot before it (simple DFS). */
    private function order(): array
    {
        $done = [];
        $order = [];
        $man = $this->manifests;
        $visit = function (string $slug) use (&$visit, &$done, &$order, $man): void {
            if (isset($done[$slug]) || !isset($man[$slug])) return;
            $done[$slug] = true;
            foreach (array_keys($man[$slug]['requires'] ?? []) as $dep) {
                if (isset($man[$dep])) $visit($dep);
            }
            $order[] = $slug;
        };
        foreach (array_keys($man) as $s) $visit($s);
        return $order;
    }

    /** @return array<string,array> discovered manifests (for the CLI/diagnostics) */
    public function manifests(): array { return $this->manifests; }
}
