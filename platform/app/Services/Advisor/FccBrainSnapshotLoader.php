<?php

declare(strict_types=1);

namespace Avc\Services\Advisor;

final class FccBrainSnapshotLoader
{
    private static array $cache = [];

    public function __construct(private array $config)
    {
    }

    public function productMatrix(): array
    {
        return $this->loadJsonSnapshot('fcc_product_advisor_matrix.json');
    }

    public function themeCatalog(): array
    {
        return $this->loadJsonSnapshot('fcc_product_advisor_theme_catalog.json');
    }

    public function directProductLookupCatalog(): array
    {
        return $this->loadJsonSnapshot('fcc_direct_product_lookup_catalog.json');
    }

    private function loadJsonSnapshot(string $filename): array
    {
        $path = rtrim((string) ($this->config['app_root'] ?? ''), '/') . '/data/advisor/' . $filename;
        if ($path === '' || !is_file($path)) {
            return [];
        }

        if (isset(self::$cache[$path])) {
            return self::$cache[$path];
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        self::$cache[$path] = is_array($decoded) ? $decoded : [];

        return self::$cache[$path];
    }
}
