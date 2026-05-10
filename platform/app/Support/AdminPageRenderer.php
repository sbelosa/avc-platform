<?php

declare(strict_types=1);

namespace Avc\Support;

final class AdminPageRenderer
{
    public static function render(string $title, string $activeItem, string $content, array $options = []): string
    {
        $pageTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $eyebrow = htmlspecialchars((string) ($options['eyebrow'] ?? 'AVC Admin'), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars((string) ($options['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $actions = (string) ($options['actions'] ?? '');
        $extraHead = (string) ($options['extra_head'] ?? '');
        $body = '<div class="admin-shell">'
            . '<aside class="admin-sidebar">'
            . '<a class="admin-brand" href="/admin"><span class="admin-brand-mark">AVC</span><span><strong>Aloe Vera Centar</strong><small>Admin</small></span></a>'
            . '<nav class="admin-nav" aria-label="Admin navigacija">'
            . self::navLink('/admin', 'Nadzorna', 'dashboard', $activeItem)
            . self::navLink('/admin/posts', 'Clanci', 'posts', $activeItem)
            . self::navLink('/admin/products', 'Proizvodi', 'products', $activeItem)
            . self::navLink('/admin/pages', 'Stranice', 'pages', $activeItem)
            . self::navLink('/admin/media', 'Media', 'media', $activeItem)
            . self::navLink('/admin/content-pipeline', 'Pipeline', 'pipeline', $activeItem)
            . self::navLink('/admin/seo-audit', 'SEO audit', 'seo', $activeItem)
            . self::navLink('/admin/product-routing', 'Routing', 'routing', $activeItem)
            . self::navLink('/admin/ai-leads', 'Leadovi', 'leads', $activeItem)
            . self::navLink('/admin/discount-leads', 'Popusti', 'discounts', $activeItem)
            . '</nav>'
            . '<div class="admin-sidebar-footer"><a href="/" target="_blank" rel="noopener">Pogledaj site</a><a href="/admin/logout">Odjava</a></div>'
            . '</aside>'
            . '<main class="admin-main">'
            . '<header class="admin-topbar"><div><div class="admin-eyebrow">' . $eyebrow . '</div><h1>' . $pageTitle . '</h1>'
            . ($description !== '' ? '<p>' . $description . '</p>' : '')
            . '</div><div class="admin-topbar-actions">' . $actions . '</div></header>'
            . $content
            . '</main>'
            . '</div>';

        return PageRenderer::render($title . ' | AVC Admin', $body, [
            'lang' => 'hr',
            'robots' => 'noindex,nofollow',
            'body_class' => 'avc-admin',
            'extra_head' => '<style>' . self::css() . '</style>' . $extraHead,
        ]);
    }

    private static function navLink(string $href, string $label, string $item, string $activeItem): string
    {
        $isActive = $item === $activeItem;

        return '<a class="' . ($isActive ? 'is-active' : '') . '" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">'
            . '<span class="admin-nav-icon" aria-hidden="true">' . htmlspecialchars(self::icon($item), ENT_QUOTES, 'UTF-8') . '</span>'
            . '<span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>'
            . '</a>';
    }

    private static function icon(string $item): string
    {
        return match ($item) {
            'dashboard' => 'D',
            'posts' => 'P',
            'products' => 'R',
            'pages' => 'S',
            'media' => 'M',
            'pipeline' => '*',
            'seo' => 'O',
            'routing' => '>',
            'leads' => '@',
            'discounts' => '%',
            default => '-',
        };
    }

    private static function css(): string
    {
        return <<<'CSS'
            body.avc-admin{
                --admin-bg:#f0f2f4;
                --admin-surface:#fff;
                --admin-sidebar:#1d2327;
                --admin-sidebar-2:#2c3338;
                --admin-text:#1d2327;
                --admin-muted:#646970;
                --admin-border:#c3c4c7;
                --admin-blue:#2271b1;
                --admin-blue-dark:#135e96;
                --admin-green:#008a20;
                --admin-red:#b32d2e;
                background:var(--admin-bg);
                color:var(--admin-text);
                font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
                line-height:1.45;
            }
            body.avc-admin a{color:var(--admin-blue);text-decoration:none}
            body.avc-admin a:hover{text-decoration:underline}
            .admin-shell{display:grid;grid-template-columns:248px minmax(0,1fr);min-height:100vh}
            .admin-sidebar{position:sticky;top:0;align-self:start;min-height:100vh;background:var(--admin-sidebar);color:#f0f0f1;display:grid;grid-template-rows:auto 1fr auto}
            .admin-brand{display:flex;align-items:center;gap:12px;padding:18px 18px;color:#fff;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.08)}
            .admin-brand:hover{text-decoration:none}
            .admin-brand-mark{display:grid;place-items:center;width:36px;height:36px;background:#72aee6;color:#0a4b78;font-weight:800;border-radius:4px}
            .admin-brand strong,.admin-brand small{display:block}
            .admin-brand small{color:#a7aaad;font-size:12px;margin-top:2px}
            .admin-nav{padding:10px 0}
            .admin-nav a{display:grid;grid-template-columns:34px 1fr;align-items:center;min-height:42px;padding:0 16px;color:#c3c4c7;text-decoration:none;border-left:4px solid transparent}
            .admin-nav a:hover{background:var(--admin-sidebar-2);color:#fff;text-decoration:none}
            .admin-nav a.is-active{background:#0a4b78;color:#fff;border-left-color:#72aee6}
            .admin-nav-icon{font-size:18px;text-align:center}
            .admin-sidebar-footer{display:grid;gap:6px;padding:14px 18px;border-top:1px solid rgba(255,255,255,.08)}
            .admin-sidebar-footer a{color:#c3c4c7;font-size:13px}
            .admin-main{min-width:0;padding:24px 28px 48px}
            .admin-topbar{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:18px}
            .admin-eyebrow{font-size:12px;color:var(--admin-muted);text-transform:uppercase;font-weight:700;letter-spacing:.04em}
            .admin-topbar h1{margin:2px 0 6px;font-size:28px;line-height:1.2;font-weight:500;letter-spacing:0}
            .admin-topbar p{margin:0;color:var(--admin-muted);max-width:760px}
            .admin-topbar-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
            .admin-button{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:6px 12px;border:1px solid var(--admin-border);border-radius:3px;background:#f6f7f7;color:#1d2327;font:inherit;cursor:pointer;text-decoration:none}
            .admin-button:hover{background:#fff;text-decoration:none}
            .admin-button-primary{background:var(--admin-blue);border-color:var(--admin-blue);color:#fff}
            .admin-button-primary:hover{background:var(--admin-blue-dark);color:#fff}
            .admin-notice{margin:0 0 14px;padding:10px 12px;border-left:4px solid var(--admin-green);background:#fff;color:#1d2327;box-shadow:0 1px 1px rgba(0,0,0,.04)}
            .admin-notice-error{border-left-color:var(--admin-red);background:#fcf0f1}
            .admin-panel{background:var(--admin-surface);border:1px solid var(--admin-border);box-shadow:0 1px 1px rgba(0,0,0,.04);margin-bottom:16px}
            .admin-panel-header{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:12px 14px;border-bottom:1px solid #dcdcde}
            .admin-panel-header h2{margin:0;font-size:16px;font-weight:600}
            .admin-panel-body{padding:14px}
            .admin-panel-body > p:first-child{margin-top:0}
            .admin-panel-body > p:last-child{margin-bottom:0}
            .admin-tabs{display:flex;gap:0;margin:0 0 14px;border-bottom:1px solid var(--admin-border);flex-wrap:wrap}
            .admin-tabs a{padding:10px 14px;border:1px solid transparent;border-bottom:none;margin-bottom:-1px;color:#50575e;text-decoration:none}
            .admin-tabs a.is-active{background:#fff;border-color:var(--admin-border);color:#000}
            .admin-filter-bar{display:grid;grid-template-columns:160px 160px minmax(220px,1fr) auto;gap:8px;align-items:end}
            .admin-filter-bar.admin-filter-wide{grid-template-columns:150px 140px 140px minmax(220px,1fr) auto}
            .admin-filter-bar label{display:grid;gap:4px;font-size:12px;color:var(--admin-muted)}
            .admin-filter-bar input,.admin-filter-bar select{min-height:32px;padding:5px 8px;border:1px solid #8c8f94;border-radius:3px;background:#fff;color:#2c3338;font:inherit}
            .admin-form-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;align-items:end}
            .admin-form-grid label{display:grid;gap:5px;color:var(--admin-muted);font-size:13px}
            .admin-form-grid input,.admin-form-grid select,.admin-form-grid textarea{width:100%;min-height:32px;padding:5px 8px;border:1px solid #8c8f94;border-radius:3px;background:#fff;color:#2c3338;font:inherit}
            .admin-form-grid textarea{min-height:88px;resize:vertical}
            .admin-list-table{width:100%;border-collapse:collapse;background:#fff}
            .admin-list-table th,.admin-list-table td{padding:9px 10px;border-bottom:1px solid #dcdcde;text-align:left;vertical-align:top;font-size:13px}
            .admin-list-table th{font-weight:600;color:#2c3338;background:#f6f7f7}
            .admin-list-table tr:hover td{background:#f6f7f7}
            .admin-table-wrap{overflow:auto}
            .admin-title-cell strong{display:block;font-size:14px;color:#1d2327}
            .admin-row-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:5px;font-size:12px}
            .admin-row-actions a{color:var(--admin-blue)}
            .admin-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
            .admin-inline-form{display:inline-flex;gap:6px;align-items:center;margin:0;flex-wrap:wrap}
            .admin-inline-form select,.admin-inline-form input{min-height:32px;padding:5px 8px;border:1px solid #8c8f94;border-radius:3px;background:#fff;color:#2c3338;font:inherit}
            .admin-muted{color:var(--admin-muted)}
            .admin-status{display:inline-flex;align-items:center;min-height:22px;padding:2px 7px;border-radius:999px;font-size:12px;background:#f0f0f1;color:#50575e}
            .admin-status-published{background:#edfaef;color:#006b1b}
            .admin-badge-row{display:flex;gap:5px;flex-wrap:wrap}
            .admin-badge{display:inline-flex;align-items:center;min-height:22px;padding:2px 7px;border-radius:999px;font-size:12px;background:#f0f0f1;color:#50575e}
            .admin-badge-good{background:#edfaef;color:#006b1b}
            .admin-badge-medium{background:#fff8e5;color:#8a5a00}
            .admin-badge-high{background:#fcf0f1;color:var(--admin-red)}
            .admin-score{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:26px;border-radius:999px;font-weight:700;background:#edfaef;color:#006b1b}
            .admin-score-medium{background:#fff8e5;color:#8a5a00}
            .admin-score-low{background:#fcf0f1;color:var(--admin-red)}
            .admin-kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:16px}
            .admin-kpi{background:#fff;border:1px solid var(--admin-border);padding:14px}
            .admin-kpi span{display:block;color:var(--admin-muted);font-size:12px;text-transform:uppercase;font-weight:700}
            .admin-kpi strong{display:block;margin-top:4px;font-size:24px;font-weight:500}
            .admin-card-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:16px}
            .admin-quick-list{display:grid;gap:8px}
            .admin-quick-row{display:grid;gap:4px;padding:10px;border:1px solid #dcdcde;background:#f6f7f7}
            .admin-quick-row strong{font-size:14px}
            .admin-quick-row span{color:var(--admin-muted);font-size:13px}
            .admin-empty{padding:28px;text-align:center;color:var(--admin-muted)}
            @media (max-width: 980px){
                .admin-shell{grid-template-columns:1fr}
                .admin-sidebar{position:static;min-height:auto}
                .admin-nav{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}
                .admin-sidebar-footer{display:flex}
                .admin-filter-bar,.admin-filter-bar.admin-filter-wide,.admin-form-grid{grid-template-columns:1fr}
                .admin-kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
                .admin-card-grid{grid-template-columns:1fr}
            }
            @media (max-width: 640px){
                .admin-main{padding:18px 12px 36px}
                .admin-topbar{display:grid}
                .admin-nav{grid-template-columns:1fr}
                .admin-list-table{min-width:760px}
                .admin-table-wrap{overflow:auto}
                .admin-kpi-grid{grid-template-columns:1fr}
            }
        CSS;
    }
}
