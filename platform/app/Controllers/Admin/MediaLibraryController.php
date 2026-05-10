<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Support\AdminPageRenderer;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class MediaLibraryController
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $query = trim((string) $this->request->input('q', ''));
        $selectFor = (int) $this->request->input('select_for', 0);
        $items = $this->filterItems($this->scanMediaLibrary(), $query);
        $body = '<div class="admin-panel"><div class="admin-panel-header"><h2>Media library</h2></div><div class="admin-panel-body">'
            . '<form class="admin-filter-bar admin-media-filter" method="get" action="/admin/media">'
            . ($selectFor > 0 ? '<input type="hidden" name="select_for" value="' . $selectFor . '">' : '')
            . '<label>Pretraga<input type="text" name="q" value="' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '" placeholder="Naziv datoteke ili putanja"></label>'
            . '<button class="admin-button admin-button-primary" type="submit">Filtriraj</button>'
            . '</form></div></div>'
            . '<div class="admin-media-grid">'
            . $this->renderMediaItems($items, $selectFor)
            . '</div>';

        $this->response->html(AdminPageRenderer::render('Media', 'media', $body, [
            'eyebrow' => 'AVC Admin',
            'description' => $selectFor > 0
                ? 'Odaberi sliku za content translation #' . $selectFor
                : 'Pregled lokalnih WordPress uploadova i AVC media datoteka.',
            'actions' => $selectFor > 0 ? '<a class="admin-button" href="/admin/content/edit?id=' . $selectFor . '">Natrag na editor</a>' : '',
            'extra_head' => '<style>' . $this->css() . '</style>',
        ]));
    }

    private function scanMediaLibrary(): array
    {
        $publicRoot = rtrim((string) ($this->config['app_root'] ?? ''), '/') . '/public';
        $roots = [
            $publicRoot . '/wp-content/uploads' => '/wp-content/uploads',
            $publicRoot . '/media' => '/media',
        ];
        $items = [];

        foreach ($roots as $filesystemRoot => $publicPrefix) {
            if (!is_dir($filesystemRoot)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($filesystemRoot, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file instanceof SplFileInfo || !$file->isFile()) {
                    continue;
                }

                $extension = strtolower($file->getExtension());
                if (!in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                    continue;
                }

                $absolutePath = $file->getPathname();
                $relativePath = ltrim(substr($absolutePath, strlen($filesystemRoot)), DIRECTORY_SEPARATOR);
                $url = $this->buildPublicUrl($publicPrefix, $relativePath);
                $items[] = [
                    'url' => $url,
                    'filename' => $file->getFilename(),
                    'relative_path' => trim($publicPrefix . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath), '/'),
                    'size' => $file->getSize(),
                    'modified_at' => $file->getMTime(),
                ];
            }
        }

        usort($items, static fn (array $left, array $right): int => (int) ($right['modified_at'] ?? 0) <=> (int) ($left['modified_at'] ?? 0));

        return array_slice($items, 0, 240);
    }

    private function filterItems(array $items, string $query): array
    {
        if ($query === '') {
            return $items;
        }

        $needle = mb_strtolower($query);

        return array_values(array_filter($items, static function (array $item) use ($needle): bool {
            return str_contains(mb_strtolower((string) ($item['filename'] ?? '')), $needle)
                || str_contains(mb_strtolower((string) ($item['relative_path'] ?? '')), $needle);
        }));
    }

    private function renderMediaItems(array $items, int $selectFor): string
    {
        if ($items === []) {
            return '<div class="admin-panel admin-media-empty"><div class="admin-empty">Nema pronađenih slika za zadane filtere.</div></div>';
        }

        $html = '';

        foreach ($items as $item) {
            $url = (string) ($item['url'] ?? '');
            $filename = (string) ($item['filename'] ?? '');
            $relativePath = (string) ($item['relative_path'] ?? '');
            $html .= '<article class="admin-media-card">'
                . '<a class="admin-media-thumb" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener"><img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" alt=""></a>'
                . '<div class="admin-media-meta"><strong>' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '</strong>'
                . '<span>' . htmlspecialchars('/' . $relativePath, ENT_QUOTES, 'UTF-8') . '</span>'
                . '<span>' . htmlspecialchars($this->formatBytes((int) ($item['size'] ?? 0)), ENT_QUOTES, 'UTF-8') . '</span></div>'
                . ($selectFor > 0
                    ? '<form method="post" action="/admin/content/featured-image"><input type="hidden" name="content_translation_id" value="' . $selectFor . '"><input type="hidden" name="featured_image_path" value="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"><button class="admin-button admin-button-primary" type="submit">Koristi sliku</button></form>'
                    : '<a class="admin-button" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">Otvori</a>')
                . '</article>';
        }

        return $html;
    }

    private function buildPublicUrl(string $publicPrefix, string $relativePath): string
    {
        $segments = array_map('rawurlencode', explode('/', str_replace(DIRECTORY_SEPARATOR, '/', trim($relativePath, DIRECTORY_SEPARATOR))));

        return rtrim($publicPrefix, '/') . '/' . implode('/', $segments);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    private function css(): string
    {
        return <<<'CSS'
            .admin-media-filter{grid-template-columns:minmax(240px,1fr) auto}
            .admin-media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:14px}
            .admin-media-card{background:#fff;border:1px solid #c3c4c7;box-shadow:0 1px 1px rgba(0,0,0,.04);display:grid;gap:10px;padding:10px;min-width:0}
            .admin-media-thumb{display:block;aspect-ratio:4/3;background:#f6f7f7;border:1px solid #dcdcde;overflow:hidden}
            .admin-media-thumb img{width:100%;height:100%;object-fit:cover;display:block}
            .admin-media-meta{display:grid;gap:3px;min-width:0}
            .admin-media-meta strong,.admin-media-meta span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
            .admin-media-meta strong{font-size:13px;color:#1d2327}
            .admin-media-meta span{font-size:12px;color:#646970}
            .admin-media-card .admin-button{width:100%}
            .admin-media-empty{grid-column:1/-1}
            @media (max-width: 640px){
                .admin-media-filter{grid-template-columns:1fr}
            }
        CSS;
    }
}
