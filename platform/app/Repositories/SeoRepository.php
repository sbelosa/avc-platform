<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class SeoRepository
{
    public function __construct(private array $config)
    {
    }

    public function listSitemapEntries(?string $languageCode = null, int $limit = 50000): array
    {
        $rows = $this->fetchSeoRows($languageCode, null, '', $limit, null, true);

        foreach ($this->staticSitemapEntries($languageCode) as $entry) {
            $rows[] = $entry;
        }

        return array_slice($rows, 0, max(1, min($limit, 50000)));
    }

    public function listAuditRows(?string $languageCode = null, ?string $contentType = null, string $query = '', int $limit = 150): array
    {
        return $this->fetchSeoRows($languageCode, $contentType, $query, $limit, null, false);
    }

    public function findAuditRow(int $contentTranslationId): ?array
    {
        $rows = $this->fetchSeoRows(null, null, '', 1, $contentTranslationId, false);

        return $rows[0] ?? null;
    }

    private function fetchSeoRows(
        ?string $languageCode,
        ?string $contentType,
        string $query,
        int $limit,
        ?int $contentTranslationId,
        bool $indexableOnly
    ): array {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(1, min($limit, 50000));
        $whereClauses = [
            "cr.is_primary = 1",
            "cr.route_type = 'content'",
            'cr.http_status_code = 200',
            "ci.lifecycle_status = 'published'",
        ];
        $types = '';
        $params = [];

        if ($languageCode !== null && trim($languageCode) !== '') {
            $whereClauses[] = 'ct.language_code = ?';
            $types .= 's';
            $params[] = trim($languageCode);
        }

        if ($contentType !== null && trim($contentType) !== '') {
            $whereClauses[] = 'ci.content_type = ?';
            $types .= 's';
            $params[] = trim($contentType);
        }

        if ($contentTranslationId !== null && $contentTranslationId > 0) {
            $whereClauses[] = 'ct.content_translation_id = ?';
            $types .= 'i';
            $params[] = $contentTranslationId;
        }

        $query = trim($query);
        if ($query !== '') {
            $like = '%' . $query . '%';
            $whereClauses[] = '(ct.title LIKE ? OR ct.slug LIKE ? OR cr.route_path LIKE ? OR sm.meta_title LIKE ? OR sm.meta_description LIKE ?)';
            $types .= 'sssss';
            array_push($params, $like, $like, $like, $like, $like);
        }

        if ($indexableOnly) {
            $whereClauses[] = 'COALESCE(sm.robots_index, 1) = 1';
            $whereClauses[] = "cr.route_path NOT REGEXP '^/(en/|sl/)?(checkout|my-account|cart|kosarica[^/]*|placanje)(/|$)'";
        }

        $sql = 'SELECT
                    ct.content_translation_id,
                    ct.content_item_id,
                    ct.language_code,
                    ct.title,
                    ct.slug,
                    ct.excerpt,
                    ct.body_html,
                    ct.summary_html,
                    ct.faq_json,
                    ct.featured_image_path,
                    ct.published_at,
                    ct.updated_at AS translation_updated_at,
                    ci.content_type,
                    ci.lifecycle_status,
                    cr.route_path,
                    cr.updated_at AS route_updated_at,
                    sm.meta_title,
                    sm.meta_description,
                    sm.canonical_url,
                    sm.robots_index,
                    sm.robots_follow,
                    sm.breadcrumb_title,
                    sm.updated_at AS seo_updated_at
                FROM content_translations ct
                INNER JOIN content_items ci
                    ON ci.content_item_id = ct.content_item_id
                INNER JOIN content_routes cr
                    ON cr.content_translation_id = ct.content_translation_id
                   AND cr.is_primary = 1
                LEFT JOIN seo_metadata sm
                    ON sm.content_translation_id = ct.content_translation_id
                WHERE ' . implode(' AND ', $whereClauses) . '
                ORDER BY COALESCE(sm.updated_at, ct.updated_at, ct.published_at) DESC
                LIMIT ' . $limit;

        $statement = $connection->prepare($sql);
        if ($types !== '') {
            $statement->bind_param($types, ...$params);
        }

        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $rows;
    }

    private function staticSitemapEntries(?string $languageCode): array
    {
        $languageFilter = strtolower(trim((string) $languageCode));
        $catalogControllerPath = dirname(__DIR__) . '/Controllers/Site/ProductCatalogController.php';
        $articleCatalogControllerPath = dirname(__DIR__) . '/Controllers/Site/ArticleCatalogController.php';
        $lastModifiedAt = max(
            (int) (@filemtime($catalogControllerPath) ?: time()),
            (int) (@filemtime($articleCatalogControllerPath) ?: time())
        );
        $lastmod = date('Y-m-d H:i:s', $lastModifiedAt);
        $entries = [
            [
                'content_translation_id' => 0,
                'content_item_id' => -1001,
                'language_code' => 'hr',
                'title' => 'Forever proizvodi',
                'slug' => 'forever-proizvodi',
                'excerpt' => 'Pregled Forever proizvoda na Aloe Vera Centru.',
                'body_html' => '',
                'summary_html' => '',
                'faq_json' => '',
                'featured_image_path' => '',
                'published_at' => $lastmod,
                'translation_updated_at' => $lastmod,
                'content_type' => 'page',
                'lifecycle_status' => 'published',
                'route_path' => '/forever-proizvodi/',
                'route_updated_at' => $lastmod,
                'meta_title' => 'Forever proizvodi | Aloe Vera Centar web shop vodič',
                'meta_description' => 'Pregledaj Forever proizvode na Aloe Vera Centru kao web shop katalog: cijene, vodiči, kategorije i siguran nastavak prema službenom Forever shopu.',
                'canonical_url' => '',
                'robots_index' => 1,
                'robots_follow' => 1,
                'breadcrumb_title' => 'Forever proizvodi',
                'seo_updated_at' => $lastmod,
            ],
            [
                'content_translation_id' => 0,
                'content_item_id' => -1001,
                'language_code' => 'en',
                'title' => 'Forever products',
                'slug' => 'forever-products',
                'excerpt' => 'Forever product catalogue on Aloe Vera Centar.',
                'body_html' => '',
                'summary_html' => '',
                'faq_json' => '',
                'featured_image_path' => '',
                'published_at' => $lastmod,
                'translation_updated_at' => $lastmod,
                'content_type' => 'page',
                'lifecycle_status' => 'published',
                'route_path' => '/en/forever-products/',
                'route_updated_at' => $lastmod,
                'meta_title' => 'Forever products | Aloe Vera Centar shop guide',
                'meta_description' => 'Browse Forever products on Aloe Vera Centar like a shop catalogue: prices, guides, categories and a safe next step to the official Forever shop.',
                'canonical_url' => '',
                'robots_index' => 1,
                'robots_follow' => 1,
                'breadcrumb_title' => 'Forever products',
                'seo_updated_at' => $lastmod,
            ],
            [
                'content_translation_id' => 0,
                'content_item_id' => -1001,
                'language_code' => 'sl',
                'title' => 'Forever izdelki',
                'slug' => 'forever-izdelki',
                'excerpt' => 'Katalog Forever izdelkov na Aloe Vera Centru.',
                'body_html' => '',
                'summary_html' => '',
                'faq_json' => '',
                'featured_image_path' => '',
                'published_at' => $lastmod,
                'translation_updated_at' => $lastmod,
                'content_type' => 'page',
                'lifecycle_status' => 'published',
                'route_path' => '/sl/forever-izdelki/',
                'route_updated_at' => $lastmod,
                'meta_title' => 'Forever izdelki | Aloe Vera Centar shop vodnik',
                'meta_description' => 'Preglej Forever izdelke na Aloe Vera Centru kot shop katalog: cene, vodniki, kategorije in varen korak do uradnega Forever shopa.',
                'canonical_url' => '',
                'robots_index' => 1,
                'robots_follow' => 1,
                'breadcrumb_title' => 'Forever izdelki',
                'seo_updated_at' => $lastmod,
            ],
            [
                'content_translation_id' => 0,
                'content_item_id' => -1002,
                'language_code' => 'hr',
                'title' => 'Članci',
                'slug' => 'clanci',
                'excerpt' => 'Korisni članci Aloe Vera Centra za lakši odabir Forever proizvoda.',
                'body_html' => '',
                'summary_html' => '',
                'faq_json' => '',
                'featured_image_path' => '',
                'published_at' => $lastmod,
                'translation_updated_at' => $lastmod,
                'content_type' => 'page',
                'lifecycle_status' => 'published',
                'route_path' => '/clanci/',
                'route_updated_at' => $lastmod,
                'meta_title' => 'Članci | Aloe Vera Centar vodiči za Forever proizvode',
                'meta_description' => 'Pregled korisnih članaka Aloe Vera Centra: probava, koža, rutina, usporedbe i objašnjenja koja pomažu odabrati Forever proizvod s više sigurnosti.',
                'canonical_url' => '',
                'robots_index' => 1,
                'robots_follow' => 1,
                'breadcrumb_title' => 'Članci',
                'seo_updated_at' => $lastmod,
            ],
            [
                'content_translation_id' => 0,
                'content_item_id' => -1002,
                'language_code' => 'en',
                'title' => 'Articles',
                'slug' => 'articles',
                'excerpt' => 'Useful Aloe Vera Centar articles for easier Forever product choices.',
                'body_html' => '',
                'summary_html' => '',
                'faq_json' => '',
                'featured_image_path' => '',
                'published_at' => $lastmod,
                'translation_updated_at' => $lastmod,
                'content_type' => 'page',
                'lifecycle_status' => 'published',
                'route_path' => '/en/articles/',
                'route_updated_at' => $lastmod,
                'meta_title' => 'Articles | Aloe Vera Centar Forever product guides',
                'meta_description' => 'Browse useful Aloe Vera Centar articles about digestion, skin, routines, comparisons and explanations that help you choose a Forever product with more confidence.',
                'canonical_url' => '',
                'robots_index' => 1,
                'robots_follow' => 1,
                'breadcrumb_title' => 'Articles',
                'seo_updated_at' => $lastmod,
            ],
            [
                'content_translation_id' => 0,
                'content_item_id' => -1002,
                'language_code' => 'sl',
                'title' => 'Članki',
                'slug' => 'clanki',
                'excerpt' => 'Uporabni članki Aloe Vera Centra za lažjo izbiro Forever izdelkov.',
                'body_html' => '',
                'summary_html' => '',
                'faq_json' => '',
                'featured_image_path' => '',
                'published_at' => $lastmod,
                'translation_updated_at' => $lastmod,
                'content_type' => 'page',
                'lifecycle_status' => 'published',
                'route_path' => '/sl/clanki/',
                'route_updated_at' => $lastmod,
                'meta_title' => 'Članki | Aloe Vera Centar vodniki za Forever izdelke',
                'meta_description' => 'Preglej uporabne članke Aloe Vera Centra o prebavi, koži, rutini, primerjavah in razlagah, ki pomagajo lažje izbrati Forever izdelek.',
                'canonical_url' => '',
                'robots_index' => 1,
                'robots_follow' => 1,
                'breadcrumb_title' => 'Članki',
                'seo_updated_at' => $lastmod,
            ],
        ];

        if ($languageFilter === '') {
            return $entries;
        }

        return array_values(array_filter($entries, static fn (array $entry): bool => strtolower((string) ($entry['language_code'] ?? '')) === $languageFilter));
    }
}
