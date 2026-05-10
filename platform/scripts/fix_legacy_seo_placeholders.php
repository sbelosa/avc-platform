<?php

declare(strict_types=1);

use Avc\Core\Database;

$rootPath = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($rootPath): void {
    $prefix = 'Avc\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = $rootPath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});

$config = require $rootPath . '/config/app.php';
$connection = Database::connection($config);
if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

$optimizedTitles = [
    '/aloe-first-njegujuci-sprej/' => 'Forever First sprej za kožu i kosu | Aloe Vera Centar',
    '/en/forever-first-nourishing-spray-for-complete-skin-and-hair-care/' => 'Forever First Spray for Skin and Hair | Aloe Vera Center',
    '/sl/forever-first-hranilni-sprej-za-popolno-nego-koze-in-las/' => 'Forever First sprej za kožo in lase | Aloe Vera Center',
    '/proizvod/aloe-berry-nectar-mini-0-33-dcl/' => 'Aloe Berry Nectar mini 0.33 dcl | 15% popusta',
    '/en/proizvod/aloe-berry-nectar-mini-0-33-dcl/' => 'Aloe Berry Nectar Mini 0.33 dcl | 15% Discount',
    '/sl/proizvod/aloe-berry-nectar-mini-033-dcl/' => 'Aloe Berry Nectar mini 0,33 dcl | 15% popust',
    '/proizvod/aloe-peaches-330-ml/' => 'Aloe Peaches 330 ml | 15% popusta i narudžba',
    '/en/proizvod/aloe-peaches-330-ml/' => 'Aloe Peaches 330 ml | 15% Discount and Order',
    '/sl/proizvod/aloe-breskve-330-ml/' => 'Aloe Breskve 330 ml | 15% popust in naročilo',
    '/proizvod/forever-fiber-vlakna/' => 'Forever Fiber vlakna | 15% popusta i narudžba',
    '/en/proizvod/forever-fiber-fiber/' => 'Forever Fiber | 15% Discount and Order',
    '/sl/proizvod/forever-fiber-vlakna/' => 'Forever Fiber vlakna | 15% popust in naročilo',
    '/proizvod/forever-lean/' => 'Forever Lean | 15% popusta i narudžba',
    '/en/proizvod/forever-lean/' => 'Forever Lean | 15% Discount and Order',
    '/sl/proizvod/za-vedno-vitko/' => 'Forever Lean | 15% popust in naročilo',
    '/proizvod/forever-lite-ultra-s-aminoteinom-cokolada/' => 'Forever Lite Ultra čokolada | 15% popusta',
    '/en/proizvod/forever-lite-ultra-with-aminotein-chocolate/' => 'Forever Lite Ultra Chocolate | 15% Discount',
    '/sl/proizvod/forever-lite-ultra-z-aminoteinsko-cokolado/' => 'Forever Lite Ultra čokolada | 15% popust',
    '/proizvod/forever-therm/' => 'Forever Therm | 15% popusta i narudžba',
    '/en/proizvod/forever-therm/' => 'Forever Therm | 15% Discount and Order',
    '/sl/proizvod/forever-therm/' => 'Forever Therm | 15% popust in naročilo',
];

$statement = $connection->prepare(
    'UPDATE seo_metadata sm
     INNER JOIN content_routes cr ON cr.content_translation_id = sm.content_translation_id AND cr.is_primary = 1
     SET sm.meta_title = ?, sm.open_graph_title = ?
     WHERE cr.route_path = ?
       AND (sm.meta_title LIKE ? OR sm.open_graph_title LIKE ?)'
);

if (!$statement) {
    fwrite(STDERR, 'Failed to prepare SEO placeholder cleanup.' . PHP_EOL);
    exit(1);
}

$placeholderPattern = '%%%title%%';
$updated = [];

foreach ($optimizedTitles as $routePath => $title) {
    $statement->bind_param('sssss', $title, $title, $routePath, $placeholderPattern, $placeholderPattern);
    $statement->execute();

    if ($statement->affected_rows > 0) {
        $updated[] = [
            'route_path' => $routePath,
            'meta_title' => $title,
        ];
    }
}

$remaining = (int) fetchScalar(
    $connection,
    "SELECT COUNT(*)
     FROM seo_metadata
     WHERE COALESCE(meta_title, '') REGEXP '%%(title|page)%%'
        OR COALESCE(open_graph_title, '') REGEXP '%%(title|page)%%'
        OR COALESCE(breadcrumb_title, '') REGEXP '%%(title|page)%%'
        OR COALESCE(meta_description, '') REGEXP '%%(title|page)%%'
        OR COALESCE(open_graph_description, '') REGEXP '%%(title|page)%%'"
);

echo json_encode([
    'updated_total' => count($updated),
    'remaining_placeholders' => $remaining,
    'updated' => $updated,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

exit($remaining > 0 ? 1 : 0);

function fetchScalar(mysqli $connection, string $sql): mixed
{
    $result = $connection->query($sql);
    $row = $result ? $result->fetch_row() : null;

    return $row[0] ?? null;
}
