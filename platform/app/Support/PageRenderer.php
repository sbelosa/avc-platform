<?php

declare(strict_types=1);

namespace Avc\Support;

final class PageRenderer
{
    public static function render(string $title, string $body, array $options = []): string
    {
        $lang = htmlspecialchars((string) ($options['lang'] ?? 'hr'), ENT_QUOTES, 'UTF-8');
        $bodyClass = trim((string) ($options['body_class'] ?? ''));
        $metaDescription = trim((string) ($options['meta_description'] ?? ''));
        $canonicalUrl = trim((string) ($options['canonical_url'] ?? ''));
        $robots = trim((string) ($options['robots'] ?? 'index,follow'));
        $openGraph = is_array($options['open_graph'] ?? null) ? $options['open_graph'] : [];
        $extraHead = (string) ($options['extra_head'] ?? '');
        $analyticsHead = self::googleTagHead($options);
        $analyticsBody = self::analyticsBodyScript($options);
        $head = '';

        if ($metaDescription !== '') {
            $head .= '<meta name="description" content="' . htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') . '">';
        }

        if ($canonicalUrl !== '') {
            $head .= '<link rel="canonical" href="' . htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') . '">';
        }

        $head .= '<meta name="robots" content="' . htmlspecialchars($robots, ENT_QUOTES, 'UTF-8') . '">';

        foreach (($options['alternate_links'] ?? []) as $alternateLink) {
            $hrefLang = htmlspecialchars((string) ($alternateLink['hreflang'] ?? ''), ENT_QUOTES, 'UTF-8');
            $href = htmlspecialchars((string) ($alternateLink['href'] ?? ''), ENT_QUOTES, 'UTF-8');

            if ($hrefLang !== '' && $href !== '') {
                $head .= '<link rel="alternate" hreflang="' . $hrefLang . '" href="' . $href . '">';
            }
        }

        if ($openGraph !== []) {
            $ogFields = [
                'og:type' => (string) ($openGraph['type'] ?? 'website'),
                'og:site_name' => (string) ($openGraph['site_name'] ?? ''),
                'og:title' => (string) ($openGraph['title'] ?? ''),
                'og:description' => (string) ($openGraph['description'] ?? ''),
                'og:url' => (string) ($openGraph['url'] ?? ''),
                'og:image' => (string) ($openGraph['image'] ?? ''),
                'og:locale' => (string) ($openGraph['locale'] ?? ''),
                'twitter:card' => (string) ($openGraph['image'] ?? '') !== '' ? 'summary_large_image' : 'summary',
                'twitter:title' => (string) ($openGraph['title'] ?? ''),
                'twitter:description' => (string) ($openGraph['description'] ?? ''),
                'twitter:image' => (string) ($openGraph['image'] ?? ''),
            ];

            foreach ($ogFields as $property => $value) {
                $value = trim($value);
                if ($value === '') {
                    continue;
                }

                $attribute = str_starts_with($property, 'twitter:') ? 'name' : 'property';
                $head .= '<meta ' . $attribute . '="' . htmlspecialchars($property, ENT_QUOTES, 'UTF-8') . '" content="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">';
            }
        }

        if (!empty($options['schema_json'])) {
            $head .= '<script type="application/ld+json">' . (string) $options['schema_json'] . '</script>';
        }

        return '<!doctype html><html lang="' . $lang . '"><head>'
            . '<meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>'
            . $head
            . $analyticsHead
            . $extraHead
            . '<style>
                :root{
                    --bg:#f6f1e8;
                    --paper:#fffdf9;
                    --paper-strong:#fffaf2;
                    --ink:#1f3428;
                    --muted:#6a7a70;
                    --line:#dfd2bc;
                    --accent:#2f7a57;
                    --accent-soft:#ddefdf;
                    --gold:#b6853b;
                    --shadow:0 18px 44px rgba(48,54,36,.08);
                    --radius:22px;
                    --font-ui:"Avenir Next","Segoe UI","Helvetica Neue",sans-serif;
                    --font-display:"Iowan Old Style","Palatino Linotype","Book Antiqua",serif;
                }
                *{box-sizing:border-box}
                html{scroll-behavior:smooth}
                body{
                    margin:0;
                    font-family:var(--font-ui);
                    line-height:1.6;
                    color:var(--ink);
                    background:
                        radial-gradient(circle at top left, rgba(182,133,59,.18), transparent 28%),
                        linear-gradient(180deg, #fbf7f1 0%, var(--bg) 48%, #f4eee3 100%);
                }
                body.site-home{
                    background:
                        radial-gradient(circle at 12% 16%, rgba(47,122,87,.09), transparent 20%),
                        radial-gradient(circle at 85% 14%, rgba(182,133,59,.12), transparent 24%),
                        linear-gradient(180deg, #fcf8f1 0%, #f5eee1 48%, #efe6d7 100%);
                }
                body.site-home .site-header{padding:14px 0 10px}
                body.site-home .hero{padding:10px 0 14px}
                a{color:var(--accent);text-decoration-color:rgba(47,122,87,.28)}
                img{display:block;max-width:100%;height:auto;border-radius:18px}
                .shell{width:min(1160px, calc(100% - 32px));margin:0 auto}
                .site-header{padding:20px 0 14px}
                .header-card{
                    display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap;
                    background:rgba(255,253,249,.82);backdrop-filter:blur(10px);
                    border:1px solid rgba(223,210,188,.75);border-radius:999px;padding:14px 20px;box-shadow:var(--shadow)
                }
                .brand{display:flex;gap:14px;align-items:center;text-decoration:none;color:inherit;min-width:0}
                .brand-lockup{display:grid;grid-template-columns:auto minmax(0,1fr);gap:12px;align-items:center;min-width:0}
                .brand-copy{display:grid;gap:4px;min-width:0}
                .brand-name{display:block;font-size:15px;line-height:1.1;font-weight:800;letter-spacing:.02em}
                .brand-logo,.showcase-brand-logo{border-radius:0}
                .brand-logo{width:min(116px, 24vw);height:auto}
                .brand-tagline{display:block;font-size:13px;color:var(--muted);font-family:var(--font-ui)}
                .header-links{display:flex;gap:10px;flex-wrap:wrap}
                .header-links a{
                    text-decoration:none;padding:10px 14px;border-radius:999px;background:#fff;
                    border:1px solid var(--line);color:var(--ink);font-size:14px;font-weight:600
                }
                .hero{padding:18px 0 16px}
                .hero-panel{
                    background:linear-gradient(180deg, rgba(255,253,249,.96), rgba(247,240,229,.98));
                    border:1px solid rgba(223,210,188,.9);border-radius:32px;padding:24px;box-shadow:var(--shadow)
                }
                .hero-kicker{
                    display:inline-flex;gap:8px;align-items:center;
                    padding:7px 12px;border-radius:999px;background:var(--accent-soft);color:var(--accent);
                    text-transform:uppercase;font-size:12px;font-weight:bold;letter-spacing:.08em;justify-self:start
                }
                .layout{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:28px;padding:8px 0 56px}
                .layout-single{grid-template-columns:minmax(0,1fr);max-width:920px}
                .content-card,.sidebar-card{
                    background:var(--paper);border:1px solid rgba(223,210,188,.88);border-radius:var(--radius);
                    box-shadow:var(--shadow)
                }
                .content-card{padding:32px}
                .sidebar{display:grid;gap:18px;align-self:start;position:sticky;top:22px}
                .sidebar-card{padding:22px;background:linear-gradient(180deg,#fffdf9,#fbf5ec)}
                .muted{color:var(--muted)}
                .eyebrow{font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:var(--gold);font-weight:bold}
                .cta-stack{display:grid;gap:12px;margin-top:18px}
                .button{
                    display:inline-flex;justify-content:center;align-items:center;gap:8px;text-decoration:none;
                    border-radius:16px;padding:13px 18px;font-weight:bold;border:1px solid transparent;font-family:var(--font-ui)
                }
                .button-primary{background:linear-gradient(135deg, #2f7a57, #44795c);color:#fff}
                .button-secondary{background:#fff;color:var(--ink);border-color:var(--line)}
                .card-link{display:inline-flex;align-items:center;gap:8px;color:var(--accent);font-weight:700;text-decoration:none}
                .info-grid{display:grid;gap:14px}
                .info-chip{display:inline-block;padding:6px 10px;border-radius:999px;background:#f2ebdd;color:#7a6139;font-size:12px;text-transform:uppercase;letter-spacing:.08em}
                .content-prose{font-size:18px;line-height:1.78}
                .content-prose h1,.content-prose h2,.content-prose h3{line-height:1.12;color:#173427;font-family:var(--font-display);letter-spacing:0}
                .content-prose h1{font-size:42px;margin:0 0 14px}
                .content-prose h2{font-size:30px;margin-top:34px}
                .content-prose h3{font-size:24px;margin-top:28px}
                .content-prose p,.content-prose ul,.content-prose ol{margin:0 0 18px}
                .content-prose blockquote{margin:24px 0;padding:16px 20px;border-left:4px solid var(--gold);background:#faf5ea;border-radius:0 16px 16px 0}
                .content-prose .lead{font-size:21px;color:var(--muted)}
                .authority-section{margin-top:30px}
                .trust-page{display:grid;gap:22px;padding:14px 0 54px}
                .trust-hero-panel{
                    display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,.44fr);gap:24px;align-items:stretch;
                    padding:28px;border-radius:30px;border:1px solid rgba(223,210,188,.9);
                    background:
                        radial-gradient(circle at 88% 12%, rgba(47,122,87,.09), transparent 24%),
                        linear-gradient(180deg,rgba(255,253,249,.98),rgba(247,240,229,.98));
                    box-shadow:var(--shadow)
                }
                .trust-hero-copy{display:grid;gap:14px;align-content:center;max-width:760px}
                .trust-hero-copy h1{font-size:48px;margin-bottom:0}
                .trust-hero-copy .lead{margin-bottom:0}
                .trust-hero-note{
                    display:grid;gap:10px;align-content:center;padding:24px;border-radius:24px;
                    border:1px solid rgba(207,226,209,.95);
                    background:linear-gradient(180deg,#eef8ef,#fff8ec);
                    box-shadow:0 16px 34px rgba(48,54,36,.055)
                }
                .trust-hero-note span,.trust-card span{
                    font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:var(--gold);font-weight:800
                }
                .trust-hero-note strong{
                    font-family:var(--font-display);font-size:28px;line-height:1.08;color:var(--ink)
                }
                .trust-hero-note p{margin:0;color:var(--muted)}
                .trust-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:4px}
                .trust-card-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
                .trust-card,.trust-section-card{
                    display:grid;gap:10px;padding:22px;border-radius:24px;border:1px solid #ebe1cf;
                    background:linear-gradient(180deg,#fffdf8,#f8f1e6);box-shadow:0 14px 30px rgba(48,54,36,.045)
                }
                .trust-card strong{font-family:var(--font-display);font-size:25px;line-height:1.08;color:var(--ink)}
                .trust-card p{margin:0;color:var(--muted)}
                .trust-section-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
                .trust-section-card h2{margin-top:0}
                .trust-list{list-style:none;padding:0;margin:0;display:grid;gap:11px}
                .trust-list li{position:relative;padding-left:28px;margin:0;color:var(--ink)}
                .trust-list li::before{
                    content:"";position:absolute;left:0;top:.55em;width:10px;height:10px;border-radius:50%;
                    background:var(--accent);box-shadow:0 0 0 6px rgba(47,122,87,.12)
                }
                .trust-cta-band{
                    display:grid;grid-template-columns:minmax(0,1fr) auto;gap:20px;align-items:center;
                    padding:24px;border-radius:26px;border:1px solid rgba(207,226,209,.95);
                    background:linear-gradient(135deg,#eef8ef,#fff8ec);box-shadow:0 16px 34px rgba(48,54,36,.055)
                }
                .trust-cta-band strong{display:block;font-family:var(--font-display);font-size:30px;line-height:1.08;margin:6px 0 8px}
                .trust-cta-band p{margin:0;color:var(--muted)}
                .goal-page{display:grid;gap:24px;padding:14px 0 56px}
                .goal-hero-panel{
                    display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,.42fr);gap:24px;align-items:stretch;
                    padding:28px;border-radius:30px;border:1px solid rgba(223,210,188,.92);
                    background:
                        radial-gradient(circle at 82% 18%, rgba(47,122,87,.1), transparent 26%),
                        linear-gradient(180deg,rgba(255,253,249,.98),rgba(247,240,229,.98));
                    box-shadow:var(--shadow)
                }
                .goal-hero-copy{display:grid;gap:14px;align-content:center;max-width:790px}
                .goal-hero-copy h1{font-size:50px;margin-bottom:0}
                .goal-hero-copy .lead{margin-bottom:0}
                .goal-path-card{
                    display:grid;gap:12px;align-content:center;padding:24px;border-radius:24px;
                    border:1px solid rgba(207,226,209,.95);
                    background:linear-gradient(180deg,#eef8ef,#fff8ec);
                    box-shadow:0 16px 34px rgba(48,54,36,.055)
                }
                .goal-path-card span,.goal-insight-card span{
                    font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:var(--gold);font-weight:800
                }
                .goal-path-card strong{font-family:var(--font-display);font-size:28px;line-height:1.08}
                .goal-path-card ol{margin:0;padding-left:22px;display:grid;gap:10px;color:var(--ink)}
                .goal-insight-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
                .goal-insight-card{
                    display:grid;gap:10px;padding:22px;border-radius:24px;border:1px solid #ebe1cf;
                    background:linear-gradient(180deg,#fffdf8,#f8f1e6);box-shadow:0 14px 30px rgba(48,54,36,.045)
                }
                .goal-insight-card strong{font-family:var(--font-display);font-size:25px;line-height:1.08}
                .goal-insight-card p{margin:0;color:var(--muted)}
                .goal-section{
                    padding:26px;border-radius:28px;border:1px solid rgba(223,210,188,.9);
                    background:rgba(255,253,249,.78);box-shadow:0 14px 30px rgba(48,54,36,.045)
                }
                .goal-section .section-heading h2{margin:0;font-family:var(--font-display);font-size:34px;line-height:1.08}
                .goal-product-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
                .goal-product-card{
                    display:grid;grid-template-columns:118px minmax(0,1fr);gap:16px;align-items:start;
                    padding:18px;border-radius:22px;border:1px solid #ebe1cf;
                    background:linear-gradient(180deg,#fffdf8,#f8f1e6)
                }
                .goal-article-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
                .goal-article-card{
                    display:grid;gap:14px;align-content:start;padding:18px;border-radius:22px;border:1px solid #ebe1cf;
                    background:linear-gradient(180deg,#fffdf8,#f8f1e6)
                }
                .goal-card-image{
                    display:block;width:100%;aspect-ratio:1/1;overflow:hidden;border-radius:18px;
                    border:1px solid #eadfce;background:radial-gradient(circle at top,#fff,#efe4d2)
                }
                .goal-article-card .goal-card-image{aspect-ratio:16/10}
                .goal-card-image img{width:100%;height:100%;object-fit:contain;border-radius:0;padding:10px}
                .goal-article-card .goal-card-image img{object-fit:cover;padding:0}
                .goal-card-image-empty{display:grid;place-items:center;color:var(--muted);font-size:13px;font-weight:800;text-align:center;padding:12px}
                .goal-card-copy{display:grid;gap:9px;min-width:0}
                .goal-card-copy h3{margin:0;font-family:var(--font-display);font-size:25px;line-height:1.08}
                .goal-card-copy h3 a{color:inherit;text-decoration:none}
                .goal-card-copy p{margin:0;color:var(--muted);font-size:15px;line-height:1.48}
                .goal-card-copy .card-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:4px}
                .goal-card-copy .button{padding:10px 13px;border-radius:14px;font-size:14px}
                .goal-advisor-band{
                    display:grid;grid-template-columns:minmax(0,1fr) auto;gap:20px;align-items:center;
                    padding:24px;border-radius:26px;border:1px solid rgba(207,226,209,.95);
                    background:linear-gradient(135deg,#eef8ef,#fff8ec);box-shadow:0 16px 34px rgba(48,54,36,.055)
                }
                .goal-advisor-band strong{display:block;font-family:var(--font-display);font-size:31px;line-height:1.08;margin:6px 0 8px}
                .goal-advisor-band p{margin:0;color:var(--muted)}
                .content-prose [data-avc-gsc-polish]{
                    margin:28px 0;padding:22px 24px;border-left:4px solid var(--accent);
                    background:linear-gradient(180deg,#f7fbf3,#fff8ec);border-radius:0 18px 18px 0;
                    box-shadow:0 14px 28px rgba(48,54,36,.055)
                }
                .content-prose [data-avc-gsc-polish] h2{margin:0 0 10px;font-size:28px}
                .content-prose [data-avc-gsc-polish] p{margin:0 0 12px}
                .content-prose [data-avc-gsc-polish] ul{margin:0;padding-left:20px}
                .content-prose [data-avc-gsc-polish] li{margin:6px 0}
                .locale-switcher{display:flex;gap:8px;flex-wrap:wrap;margin-top:18px}
                .locale-switcher a{text-decoration:none;padding:8px 11px;border-radius:999px;background:#fff;border:1px solid var(--line);font-size:13px}
                .site-footer{padding:0 0 48px}
                .site-footer .content-card{padding:22px 28px}
                .footer-links{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}
                .footer-links a{
                    display:inline-flex;padding:7px 10px;border-radius:999px;background:#fff;
                    border:1px solid var(--line);font-size:13px;font-weight:700;text-decoration:none;color:var(--ink)
                }
                .section-stack{display:grid;gap:20px;padding-bottom:56px}
                .card-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
                .story-card{
                    display:grid;gap:14px;min-width:0;padding:22px;border-radius:24px;border:1px solid #e7dbc7;
                    background:linear-gradient(180deg,#fffefa,#f8f1e5);box-shadow:0 14px 30px rgba(48,54,36,.055)
                }
                .story-card h3{margin:0;font-size:22px;line-height:1.12;font-family:var(--font-display);letter-spacing:0}
                .story-card h3 a{color:inherit;text-decoration:none}
                .story-card h3,.story-card p:not(.card-meta){display:-webkit-box;-webkit-box-orient:vertical;overflow:hidden}
                .story-card h3{-webkit-line-clamp:2}
                .story-card p{margin:0}
                .story-card p:not(.card-meta){-webkit-line-clamp:3}
                .story-card .card-meta{font-size:13px;color:var(--muted);font-weight:600}
                .story-card .card-image{
                    display:block;width:100%;max-width:100%;aspect-ratio:16/10;overflow:hidden;
                    border-radius:18px;background:#efe6d6
                }
                .story-card .card-image img{width:100%;height:100%;object-fit:cover;border-radius:0}
                .story-card .card-actions{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-top:auto;padding-top:4px}
                .story-card .button{padding:11px 15px;border-radius:14px}
                .story-card-guide .card-actions .card-link{margin-left:auto}
                .story-card-guide .card-image{
                    aspect-ratio:4/3;padding:20px;display:grid;place-items:center;
                    background:radial-gradient(circle at top, rgba(255,255,255,.96), rgba(244,234,219,.88));
                    border:1px solid #efe3d2
                }
                .story-card-guide .card-image img{
                    width:100%;height:100%;object-fit:contain;
                    filter:drop-shadow(0 16px 24px rgba(31,52,40,.08))
                }
                .story-card-article>.badge-row{display:none}
                .story-card-article .card-image{position:relative;min-height:0;border-radius:18px}
                .story-card-article .card-image img{filter:brightness(.74) saturate(.88)}
                .card-image-overlay{
                    position:absolute;inset:0;display:flex;align-items:flex-start;justify-content:flex-start;
                    padding:16px;background:linear-gradient(180deg,rgba(17,31,25,.12),rgba(17,31,25,.4));pointer-events:none
                }
                .card-image-overlay .badge{background:rgba(249,241,229,.96)}
                .badge-row{display:flex;gap:8px;flex-wrap:wrap}
                .badge{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;background:#f3eadc;color:#735c37;font-size:12px;letter-spacing:.06em;text-transform:uppercase}
                .feature-list{display:grid;gap:14px}
                .feature-row{display:grid;gap:6px;padding:14px 16px;border-radius:16px;background:#fbf7ef;border:1px solid #ebe1cf}
                .feature-row-media{grid-template-columns:72px minmax(0,1fr);gap:12px;align-items:start;padding:12px}
                .feature-row-copy{display:grid;gap:6px;min-width:0}
                .feature-row strong{font-family:var(--font-display);font-size:20px;line-height:1.15}
                .feature-thumb{
                    display:block;width:72px;aspect-ratio:1/1;border-radius:14px;overflow:hidden;
                    background:#efe6d6;border:1px solid #e7dbc7;text-decoration:none
                }
                .feature-thumb img{display:block;width:100%;height:100%;object-fit:cover}
                .feature-thumb-product{
                    display:grid;place-items:center;background:radial-gradient(circle at top, #fffdf9, #eee3d1)
                }
                .feature-thumb-product img{
                    object-fit:contain;padding:8px;box-sizing:border-box;
                    filter:drop-shadow(0 10px 16px rgba(31,52,40,.08))
                }
                .card-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
                .feature-actions{margin-top:4px}
                .feature-actions .button{min-height:44px;padding:10px 14px;border-radius:14px}
                .feature-row .muted{
                    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden
                }
                .table-wrap{overflow:auto}
                .table-wrap table{min-width:760px}
                .crumbs{display:flex;gap:10px;flex-wrap:wrap;align-items:center;font-size:14px}
                .crumbs a{text-decoration:none}
                .inline-section{display:grid;gap:16px;margin-top:34px;padding-top:30px;border-top:1px solid #ece1cf}
                .inline-section .content-prose h2{margin:0}
                .inline-section .feature-list{gap:14px}
                .inline-section .feature-row{
                    border-radius:20px;background:linear-gradient(180deg,#fffdf8,#f8f1e6);
                    box-shadow:0 12px 26px rgba(48,54,36,.045)
                }
                .inline-section .feature-row-media{grid-template-columns:92px minmax(0,1fr);gap:16px;align-items:center;padding:14px 16px}
                .inline-section .feature-thumb{width:92px;border-radius:16px}
                .inline-section .feature-row strong{font-size:24px;line-height:1.08}
                .inline-section .feature-row .muted{font-size:16px;line-height:1.55}
                .inline-section .feature-actions .button{min-height:48px}
                .inline-section-articles .feature-row-media{grid-template-columns:86px minmax(0,1fr)}
                .inline-section-articles .feature-thumb{width:86px}
                .summary-box{margin:0 0 26px;padding:22px;border-radius:22px;border:1px solid #ebe1cf;background:linear-gradient(180deg,#fdfaf3,#f8f2e7)}
                .support-cta-box{
                    margin:34px 0 30px;padding:26px;border-radius:24px;
                    display:grid;grid-template-columns:minmax(0,1fr) auto;gap:18px;align-items:center;
                    background:linear-gradient(135deg,#eff8f1 0%,#fff8eb 100%);border-color:#d9e5d6
                }
                .support-cta-box .eyebrow{grid-column:1/-1}
                .support-cta-box .content-prose h2{margin:0 0 8px;font-size:28px}
                .support-cta-box .content-prose p{margin:0;color:var(--muted)}
                .support-cta-box .card-actions{justify-content:flex-end}
                .support-cta-box .button{min-height:48px}
                .faq-list{
                    display:grid;gap:14px;margin:34px 0 30px;padding:26px;border-radius:26px;
                    border:1px solid #e5d8c4;background:linear-gradient(135deg,#fffdf9 0%,#f8f1e6 58%,#eef8f1 100%);
                    box-shadow:0 14px 30px rgba(48,54,36,.045)
                }
                .faq-list .content-prose h2{margin:0;font-size:32px}
                .faq-intro{max-width:680px;margin:-2px 0 2px;color:var(--muted);font-size:16px;line-height:1.6}
                .faq-item{
                    border:1px solid #e6dac7;border-radius:20px;background:rgba(255,253,249,.86);padding:0;
                    overflow:hidden;box-shadow:0 10px 22px rgba(48,54,36,.035)
                }
                .faq-item[open]{border-color:#d5e5d7;background:linear-gradient(180deg,#ffffff,#f7fbf3)}
                .faq-item summary{
                    cursor:pointer;list-style:none;padding:18px 18px;display:grid;grid-template-columns:minmax(0,1fr) 34px;
                    gap:16px;align-items:center;color:var(--ink)
                }
                .faq-item summary::-webkit-details-marker{display:none}
                .faq-question-text{font-family:var(--font-display);font-size:21px;line-height:1.16}
                .faq-toggle{
                    position:relative;width:34px;height:34px;border-radius:50%;border:1px solid #d7e7da;
                    background:#f4fbf4;box-shadow:inset 0 0 0 5px rgba(47,122,87,.06)
                }
                .faq-toggle::before,.faq-toggle::after{
                    content:"";position:absolute;left:50%;top:50%;width:13px;height:2px;border-radius:99px;background:var(--accent);
                    transform:translate(-50%,-50%)
                }
                .faq-toggle::after{transform:translate(-50%,-50%) rotate(90deg);transition:opacity .18s ease}
                .faq-item[open] .faq-toggle::after{opacity:0}
                .faq-answer{padding:0 18px 20px 18px}
                .faq-answer p{margin:0;color:var(--muted);font-size:16px;line-height:1.65}
                .split-hero{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(340px,.72fr);gap:22px;align-items:start}
                .hero-copy{display:grid;gap:14px;align-content:start;padding:2px 0 0}
                .hero-copy .content-prose h1{margin:0;font-size:44px}
                .hero-copy>.muted{margin:0;max-width:48rem;font-size:17px}
                .hero-meta-row{margin-top:0}
                .split-hero .cta-stack{max-width:660px}
                .split-hero .cta-stack .button{min-height:52px}
                .glass-panel{
                    padding:20px;border-radius:28px;
                    background:linear-gradient(180deg,rgba(255,253,249,.94),rgba(248,241,230,.96));
                    border:1px solid rgba(223,210,188,.9);box-shadow:0 14px 30px rgba(48,54,36,.055)
                }
                .product-hero-card{display:grid;gap:14px}
                .product-hero-media{
                    display:grid;place-items:center;margin:0;padding:20px;border-radius:24px;min-height:258px;aspect-ratio:4/3;overflow:hidden;
                    background:radial-gradient(circle at top, rgba(255,255,255,.98), rgba(246,236,220,.9));
                    border:1px solid #eadfcd;text-decoration:none
                }
                .product-hero-media img{
                    width:100%;max-height:260px;object-fit:contain;border-radius:14px;
                    filter:drop-shadow(0 16px 26px rgba(31,52,40,.09))
                }
                .product-panel-stats{gap:12px}
                .product-panel-row{
                    position:relative;grid-template-columns:18px minmax(0,1fr);gap:4px 12px;align-items:start;
                    padding:16px;border-radius:18px;background:linear-gradient(180deg,#fffaf2,#f7efe2)
                }
                .product-panel-row::before{
                    content:"";grid-row:1 / span 2;width:10px;height:10px;border-radius:50%;
                    background:var(--accent);box-shadow:0 0 0 6px rgba(47,122,87,.11);margin-top:8px
                }
                .product-panel-row strong,.product-panel-row .muted{grid-column:2}
                .product-panel-row strong{font-size:22px;line-height:1.08}
                .product-hero-panel-cta{margin-top:2px}
                .product-hero-panel-cta .button{width:100%;min-height:52px}
                .article-hero-assist{display:grid;gap:14px}
                .article-hero-assist h3{
                    margin:0;font-family:var(--font-display);font-size:28px;line-height:1.08
                }
                .article-hero-assist p{margin:0;font-size:16px;line-height:1.58}
                .article-assist-list{display:grid;gap:10px}
                .article-assist-card{
                    display:grid;gap:6px;padding:16px;border-radius:18px;
                    border:1px solid #e7dbc7;background:linear-gradient(180deg,#fffdf8,#f8f1e6)
                }
                .article-assist-card strong{font-family:var(--font-display);font-size:21px;line-height:1.08}
                .article-assist-card span{color:var(--muted);line-height:1.5}
                .conversion-box{
                    display:grid;grid-template-columns:minmax(0,1fr) auto;gap:20px;align-items:center;
                    margin:26px 0;padding:22px;border-radius:22px;border:1px solid #d8e3d6;
                    background:linear-gradient(135deg,#eff8f0,#fffaf1);box-shadow:0 14px 30px rgba(48,54,36,.055)
                }
                .conversion-box .content-prose h2{margin:6px 0 8px;font-size:28px}
                .conversion-box .content-prose p{margin:0;color:var(--muted)}
                .conversion-actions{display:grid;gap:10px;min-width:230px}
                .article-product-block{align-items:start;grid-template-columns:1fr}
                .conversion-product-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
                .conversion-product-card{
                    display:grid;gap:10px;align-content:start;padding:18px;border-radius:18px;
                    border:1px solid #e7dbc7;background:rgba(255,253,249,.86)
                }
                .conversion-product-card .feature-thumb{width:100%;aspect-ratio:4/3;border-radius:16px}
                .conversion-product-card .feature-thumb-product img{padding:14px}
                .conversion-product-card strong{font-family:var(--font-display);font-size:22px;line-height:1.08}
                .conversion-product-card p{margin:0;color:var(--muted);font-size:15px}
                .recommendation-reason{
                    display:block;margin-top:2px;padding:9px 10px;border-radius:14px;
                    background:#f4faf1;color:#315f43;border:1px solid #d7e7da;font-size:13px;line-height:1.35
                }
                .sidebar-info-card,.sidebar-buy-card{display:grid;gap:8px}
                .sidebar-info-card h3,.sidebar-buy-card h3,.sidebar-advisor-card h3{
                    margin:0;font-family:var(--font-display);font-size:24px;line-height:1.08
                }
                .sidebar-info-card p,.sidebar-buy-card p,.sidebar-advisor-card p{margin:0}
                .sidebar-buy-card{border-color:#d7e7da;background:linear-gradient(180deg,#f4fbf4,#fffdf9)}
                .sidebar-buy-card .cta-stack{margin-top:4px}
                .sidebar-buy-card .button{width:100%}
                .sidebar-related-card{display:grid;gap:14px;padding:20px;background:linear-gradient(180deg,#fffdf9,#f8f1e6)}
                .sidebar-related-card>.eyebrow{margin-bottom:0}
                .sidebar-related-card .feature-list{gap:10px}
                .sidebar-related-card .feature-row{
                    border-radius:18px;background:rgba(255,253,249,.88);
                    box-shadow:0 10px 22px rgba(48,54,36,.04)
                }
                .sidebar-related-card .feature-row-media{
                    grid-template-columns:70px minmax(0,1fr);gap:13px;align-items:start;padding:12px
                }
                .sidebar-related-card .feature-thumb{width:70px;border-radius:16px}
                .sidebar-related-card .feature-row-copy{gap:7px}
                .sidebar-related-card .feature-row strong{font-size:19px;line-height:1.08}
                .sidebar-related-card .feature-row .muted{font-size:14px;line-height:1.42;-webkit-line-clamp:2}
                .sidebar-related-card .feature-actions{display:grid;grid-template-columns:.64fr 1.16fr;gap:8px;margin-top:4px}
                .sidebar-related-card .feature-actions .button{
                    width:100%;min-height:40px;padding:9px 10px;border-radius:13px;font-size:11.5px;line-height:1.2;white-space:nowrap
                }
                .sidebar-related-products .feature-row-product{
                    position:relative;overflow:hidden;border-color:#eadfcd
                }
                .sidebar-related-products .feature-row-product::after{
                    content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#2f7a57,#b6853b);opacity:.72
                }
                .sidebar-related-products .feature-thumb-product img{padding:10px}
                .sidebar-advisor-card{
                    display:grid;gap:12px;background:linear-gradient(180deg,#fffdf9,#f7f0e4)
                }
                .sidebar-advisor-card .advisor-chat{margin-top:2px}
                .sidebar-advisor-card .advisor-chat-header{grid-template-columns:40px minmax(0,1fr);padding:10px}
                .sidebar-advisor-card .advisor-avatar{width:40px;height:40px;font-size:11px}
                .sidebar-advisor-card .advisor-chat-header strong{font-size:19px}
                .sidebar-advisor-card .advisor-chat-header span:not(.advisor-avatar){font-size:13px}
                .sidebar-advisor-card .advisor-prompt-chips button{flex:1 1 92px}
                .sidebar-advisor-card .advisor-messages{min-height:136px;max-height:360px;padding:10px}
                .sidebar-advisor-card .advisor-message{max-width:100%}
                .sidebar-advisor-card .advisor-action-row{grid-template-columns:1fr}
                .sidebar-advisor-card .advisor-input-row textarea{min-height:124px}
                .sidebar-advisor-card .advisor-action-row .button,.sidebar-advisor-card .js-advisor-toggle-lead{
                    width:100%;min-height:46px;border-radius:16px
                }
                .sidebar-advisor-card .js-advisor-toggle-lead{padding:10px 12px;font-size:13px}
                .sidebar-advisor-card .notice{border-radius:16px;background:#eff8f1}
                .mobile-shop-bar{display:none}
                .inline-form{display:flex;gap:10px;flex-wrap:wrap}
                .inline-form .button{border:none;cursor:pointer}
                table{width:100%;border-collapse:collapse}
                th,td{padding:10px 8px;border-bottom:1px solid #eee3d1;text-align:left;font-size:14px;vertical-align:top}
                form{display:grid;gap:12px}
                label{display:grid;gap:6px;font-size:14px;color:var(--muted)}
                input,textarea,select{
                    width:100%;padding:12px 14px;border-radius:14px;border:1px solid var(--line);
                    background:#fff;font:inherit;color:var(--ink)
                }
                textarea{min-height:140px;resize:vertical}
                .notice{padding:12px 14px;border-radius:14px;background:#edf7ef;color:#27543e;border:1px solid #cfe3d3;font-size:14px}
                body.discount-modal-open{overflow:hidden}
                .discount-modal[hidden]{display:none}
                .discount-modal{position:fixed;inset:0;z-index:120;display:grid;place-items:center;padding:18px}
                .discount-modal-backdrop{
                    position:absolute;inset:0;background:rgba(31,52,40,.38);backdrop-filter:blur(8px)
                }
                .discount-modal-card{
                    position:relative;z-index:1;width:min(560px,100%);max-height:min(92vh,760px);overflow:auto;
                    display:grid;gap:18px;padding:26px;border-radius:28px;border:1px solid rgba(223,210,188,.95);
                    background:
                        radial-gradient(circle at top right, rgba(47,122,87,.10), transparent 34%),
                        linear-gradient(180deg,#fffdf9,#f7f0e4);
                    box-shadow:0 28px 80px rgba(31,52,40,.28)
                }
                .discount-modal-close{
                    position:absolute;right:14px;top:12px;width:38px;height:38px;border-radius:50%;
                    border:1px solid #e0d3bd;background:#fffdf9;color:var(--ink);font:inherit;
                    font-size:24px;line-height:1;cursor:pointer
                }
                .discount-modal-head{display:grid;gap:8px;padding-right:32px}
                .discount-modal-head h2{
                    margin:0;font-family:var(--font-display);font-size:32px;line-height:1.05;color:var(--ink)
                }
                .discount-modal-head p{margin:0;font-size:16px;line-height:1.58}
                .discount-form{display:grid;gap:13px}
                .discount-contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
                .discount-consent{
                    grid-template-columns:auto minmax(0,1fr);align-items:start;gap:10px;
                    padding:13px 14px;border-radius:16px;border:1px solid #d8e7d8;background:#f1f8f1;color:var(--ink)
                }
                .discount-consent input{width:auto;margin-top:4px}
                .discount-status{
                    min-height:22px;color:#2f6f4f;font-weight:700;font-size:14px
                }
                .discount-status.is-error{color:#9d342f}
                .discount-actions{display:grid;grid-template-columns:1.12fr .88fr;gap:10px}
                .discount-actions .button{width:100%;min-height:50px}
                .discount-note{margin:0;color:var(--muted);font-size:13px;line-height:1.45}
                .advisor-chat{display:grid;gap:12px}
                .advisor-chat-header{
                    display:grid;grid-template-columns:46px minmax(0,1fr);gap:12px;align-items:center;
                    padding:12px 14px;border-radius:20px;background:linear-gradient(135deg,#eff8f1,#fffaf1);
                    border:1px solid #d8e7d8
                }
                .advisor-avatar{
                    width:46px;height:46px;border-radius:50%;display:grid;place-items:center;
                    background:linear-gradient(135deg,#2f7a57,#5f966e);color:#fff;font-size:12px;font-weight:800;letter-spacing:.04em
                }
                .advisor-chat-header strong{display:block;font-family:var(--font-display);font-size:22px;line-height:1.05}
                .advisor-chat-header span:not(.advisor-avatar){display:block;color:var(--muted);font-size:14px;line-height:1.35}
                .advisor-guide{background:#eff8f1}
                .advisor-prompt-chips{display:flex;gap:8px;flex-wrap:wrap}
                .advisor-prompt-chips button{
                    border:1px solid #d8e7d8;background:#fffdf9;color:var(--accent);border-radius:999px;
                    padding:8px 12px;font:inherit;font-size:13px;font-weight:700;cursor:pointer
                }
                .advisor-prompt-chips button:hover{background:#f4fbf4}
                .advisor-messages{
                    display:grid;gap:12px;align-content:start;min-height:188px;max-height:520px;overflow:auto;
                    padding:14px;border-radius:22px;border:1px solid #eadfcd;
                    background:linear-gradient(180deg,rgba(255,253,249,.84),rgba(248,241,230,.72))
                }
                .advisor-message{
                    display:grid;gap:8px;max-width:88%;padding:13px 15px;border-radius:18px;
                    border:1px solid #eadfcd;background:#fbf7ef;box-shadow:0 10px 24px rgba(48,54,36,.04);
                    animation:advisorBubbleIn .18s ease both
                }
                .advisor-message.assistant{justify-self:start;border-top-left-radius:8px;background:linear-gradient(180deg,#fffdf8,#f6efe1)}
                .advisor-message.user{justify-self:end;max-width:78%;border-top-right-radius:8px;background:linear-gradient(180deg,#eff7f1,#e4f1e7);border-color:#d7e7da}
                .advisor-message-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--gold);font-weight:800}
                .advisor-message p{margin:0}
                .advisor-message-body{white-space:pre-line;line-height:1.65}
                .advisor-message.is-streaming .advisor-message-body::after{
                    content:"";display:inline-block;width:7px;height:1.15em;margin-left:3px;vertical-align:-2px;
                    border-radius:99px;background:rgba(47,122,87,.42);animation:advisorCaret 1s infinite
                }
                .advisor-typing-dots{display:inline-flex;gap:5px;align-items:center;min-height:22px}
                .advisor-typing-dots span{
                    width:7px;height:7px;border-radius:50%;background:var(--accent);opacity:.35;animation:advisorDot 1.05s infinite
                }
                .advisor-typing-dots span:nth-child(2){animation-delay:.15s}
                .advisor-typing-dots span:nth-child(3){animation-delay:.3s}
                .advisor-message-extra{display:grid;gap:10px;margin-top:2px;animation:advisorBubbleIn .22s ease both}
                .advisor-message-extra .feature-list{gap:9px;margin-top:2px}
                .advisor-message-extra .feature-row{
                    border-radius:16px;background:rgba(255,253,249,.84);box-shadow:none
                }
                .advisor-message-extra .feature-row-media{grid-template-columns:58px minmax(0,1fr);gap:10px;padding:10px}
                .advisor-message-extra .feature-thumb{width:58px;border-radius:13px}
                .advisor-message-extra .feature-row strong{font-size:18px}
                .advisor-message-extra .feature-row .muted{font-size:13px;line-height:1.4;-webkit-line-clamp:2}
                .advisor-message-extra .card-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px}
                .advisor-message-extra .button{min-height:38px;padding:8px 10px;border-radius:12px;font-size:12px}
                @keyframes advisorBubbleIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
                @keyframes advisorDot{0%,80%,100%{opacity:.28;transform:translateY(0)}40%{opacity:1;transform:translateY(-3px)}}
                @keyframes advisorCaret{0%,45%{opacity:1}46%,100%{opacity:.12}}
                .advisor-meta{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;font-size:12px;color:var(--muted)}
                .advisor-feedback{display:flex;gap:8px;flex-wrap:wrap}
                .advisor-feedback button{
                    border:1px solid var(--line);background:#fff;border-radius:999px;padding:6px 10px;font:inherit;cursor:pointer;color:var(--ink)
                }
                .advisor-feedback button.is-active{background:var(--accent-soft);border-color:#bfd9c0;color:var(--accent)}
                .advisor-composer{display:grid;gap:10px}
                .advisor-input-row{display:grid;grid-template-columns:1fr}
                .advisor-input-row textarea{min-height:112px}
                .advisor-action-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:center}
                .advisor-action-row .button{min-height:46px}
                .advisor-hidden{display:none}
                .metric-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
                .metric{padding:18px;border-radius:18px;background:#fbf7ef;border:1px solid #ebe1cf}
                .metric strong{display:block;font-size:28px;font-family:var(--font-display)}
                .home-hero-panel{position:relative;overflow:hidden;padding:22px 24px}
                .home-hero-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(430px,.98fr);gap:22px;align-items:start}
                .home-hero-copy{display:grid;gap:13px;align-content:start;padding:10px 8px 2px 0}
                .home-hero-copy .content-prose h1{font-size:42px;line-height:1.02;max-width:17ch;text-wrap:balance}
                .home-hero-copy .muted{font-size:17px;line-height:1.55;max-width:34rem}
                .hero-intro{margin:0}
                .hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:0}
                .hero-note{display:flex;gap:8px;flex-wrap:wrap}
                .hero-note .badge{background:#f7efe1;padding:7px 10px;font-size:11px}
                .hero-feature-list{display:grid;gap:12px;margin-top:8px}
                .hero-feature{
                    display:grid;grid-template-columns:auto 1fr;gap:14px;align-items:start;padding:16px 18px;
                    border-radius:20px;border:1px solid rgba(223,210,188,.9);background:rgba(255,253,249,.82)
                }
                .hero-feature-mark{
                    width:12px;height:12px;border-radius:50%;background:var(--accent);
                    box-shadow:0 0 0 6px rgba(47,122,87,.12);margin-top:7px
                }
                .hero-feature strong{display:block;margin:0 0 4px;font-family:var(--font-display);font-size:24px;line-height:1.04}
                .hero-feature p{margin:0;color:var(--muted)}
                .home-showcase{display:grid;gap:10px;align-content:start}
                .showcase-media{
                    display:grid;gap:11px;align-content:start;padding:16px;border-radius:26px;
                    border:1px solid rgba(223,210,188,.95);
                    background:
                        radial-gradient(circle at top right, rgba(47,122,87,.08), transparent 34%),
                        linear-gradient(180deg, rgba(255,252,246,.98), rgba(247,239,226,.98));
                    box-shadow:0 14px 30px rgba(48,54,36,.06);overflow:hidden
                }
                .showcase-header,.showcase-brand{display:grid;gap:14px}
                .showcase-header{grid-template-columns:96px minmax(0,1fr);gap:13px;align-items:start}
                .showcase-brand-logo{width:96px;height:auto;margin-top:3px}
                .showcase-brand-copy{display:grid;gap:6px;min-width:0}
                .showcase-brand-copy strong{font-family:var(--font-display);font-size:26px;line-height:1.03;text-wrap:balance}
                .showcase-brand-copy p{margin:0;color:var(--muted);font-size:14px;line-height:1.4}
                .showcase-visual{
                    position:relative;display:block;min-height:280px;border-radius:24px;overflow:hidden;
                    background:#ede3d1;border:1px solid rgba(223,210,188,.95);text-decoration:none;color:#fff
                }
                .showcase-visual img{
                    width:100%;height:100%;min-height:280px;object-fit:cover;
                    filter:brightness(.78) saturate(.92)
                }
                .showcase-visual-overlay{
                    position:absolute;inset:0;display:grid;align-content:end;gap:10px;padding:24px;
                    background:linear-gradient(180deg,rgba(17,31,25,.08),rgba(17,31,25,.62))
                }
                .showcase-visual-overlay strong{font-family:var(--font-display);font-size:34px;line-height:1.02;max-width:12ch}
                .showcase-visual-link{font-size:14px;font-weight:700;letter-spacing:.02em}
                .showcase-shortcuts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
                .showcase-shortcut{
                    display:grid;gap:6px;padding:16px 16px 18px;border-radius:18px;text-decoration:none;color:inherit;
                    border:1px solid #e7dbc7;background:rgba(255,255,255,.76)
                }
                .showcase-shortcut strong{font-family:var(--font-display);font-size:22px;line-height:1.05}
                .showcase-shortcut p{margin:0;color:var(--muted);font-size:14px;line-height:1.5}
                .goal-selector{
                    display:grid;gap:9px;padding:11px;border-radius:20px;
                    background:rgba(255,255,255,.74);border:1px solid rgba(223,210,188,.86)
                }
                .goal-selector>div:first-child{display:grid;gap:4px}
                .goal-selector strong{font-family:var(--font-display);font-size:23px;line-height:1.06}
                .goal-selector p{margin:0;color:var(--muted);font-size:13px;line-height:1.45}
                .goal-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}
                .goal-card{
                    display:grid;gap:4px;align-content:center;min-height:60px;padding:9px 12px;border-radius:15px;
                    color:inherit;text-decoration:none;background:linear-gradient(180deg,#fffdf8,#f8f1e6);
                    border:1px solid #e7dbc7;transition:transform .16s ease, border-color .16s ease, box-shadow .16s ease
                }
                .goal-card:hover{transform:translateY(-1px);border-color:#c9d8c7;box-shadow:0 12px 24px rgba(48,54,36,.08)}
                .goal-card-title{
                    font-family:var(--font-display);font-weight:700;color:var(--ink);
                    font-size:18px;line-height:1.02
                }
                .goal-card-text{display:none}
                .goal-card-product{display:none}
                .goal-card-action{color:var(--accent);font-size:11.5px;font-weight:700;line-height:1.2}
                .goal-card-unsure{background:linear-gradient(180deg,#eef7f0,#e6f1e8);border-color:#cfe2d1}
                .showcase-feature{
                    display:grid;grid-template-columns:76px minmax(0,1fr);gap:12px;align-items:center;
                    padding:12px 14px;border-radius:18px;border:1px solid #e4d7c2;
                    background:linear-gradient(180deg,#fffdf8,#f7efe2);box-shadow:0 14px 30px rgba(48,54,36,.05)
                }
                .showcase-feature-media{
                    display:block;aspect-ratio:1/1;border-radius:15px;overflow:hidden;background:#efe5d3;
                    border:1px solid rgba(223,210,188,.95)
                }
                .showcase-feature-media img{width:100%;height:100%;object-fit:cover}
                .showcase-feature-copy{display:grid;gap:6px}
                .showcase-feature-text-only{grid-template-columns:1fr}
                .showcase-feature strong{font-family:var(--font-display);font-size:22px;line-height:1.06}
                .showcase-feature p{
                    margin:0;color:var(--muted);font-size:14px;line-height:1.38;
                    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden
                }
                .showcase-feature a{font-weight:700;text-decoration:none}
                .proof-bar{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:2px 0 20px}
                .proof-point{
                    padding:18px 20px;border-radius:20px;border:1px solid rgba(223,210,188,.95);
                    background:rgba(255,252,247,.82);box-shadow:0 12px 28px rgba(48,54,36,.04)
                }
                .proof-point strong{display:block;margin:8px 0 8px;font-family:var(--font-display);font-size:22px;line-height:1.08}
                .proof-point p{margin:0;color:var(--muted);font-size:15px}
                .section-heading{display:grid;gap:8px;margin-bottom:18px;max-width:72rem}
                .section-heading .content-prose h2{margin:0}
                .section-heading p{margin:0;color:var(--muted)}
                .intent-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
                .intent-card{
                    display:grid;gap:10px;align-content:start;padding:24px;border-radius:24px;
                    border:1px solid #ebe1cf;background:linear-gradient(180deg,#fffdf8,#f8f1e6);
                    color:inherit;text-decoration:none;box-shadow:0 14px 30px rgba(48,54,36,.05);transition:transform .18s ease, box-shadow .18s ease
                }
                .intent-card:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(48,54,36,.09)}
                .intent-card strong{font-family:var(--font-display);font-size:28px;line-height:1.04}
                .intent-card p{margin:0;color:var(--muted);font-size:15px}
                .intent-card .intent-link{margin-top:auto;font-weight:700;color:var(--accent)}
                .step-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
                .step-card{
                    position:relative;display:grid;gap:10px;padding:24px 22px 22px 74px;border-radius:24px;
                    border:1px solid #ebe1cf;background:linear-gradient(180deg,#fffdf8,#f8f1e6);box-shadow:0 14px 30px rgba(48,54,36,.05)
                }
                .step-number{
                    position:absolute;left:22px;top:22px;width:36px;height:36px;border-radius:50%;
                    display:grid;place-items:center;background:var(--accent);color:#fff;font-size:14px;font-weight:700
                }
                .step-card strong{font-family:var(--font-display);font-size:26px;line-height:1.08}
                .step-card p{margin:0;color:var(--muted)}
                .authority-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
                .authority-card{
                    display:grid;gap:10px;padding:22px;border-radius:24px;background:linear-gradient(180deg,#fffdf9,#f8f1e6);
                    border:1px solid #ebe1cf
                }
                .authority-card h3{margin:0;font-family:var(--font-display);font-size:26px;line-height:1.1}
                .authority-card p{margin:0;color:var(--muted)}
                .advisor-layout{display:grid;grid-template-columns:minmax(0,.9fr) minmax(360px,1.1fr);gap:24px;align-items:start}
                .advisor-copy{display:grid;gap:20px;align-content:start}
                .advisor-benefit-grid{display:grid;gap:14px}
                .advisor-benefit{
                    display:grid;gap:8px;padding:20px;border-radius:20px;border:1px solid #ebe1cf;
                    background:linear-gradient(180deg,#fffdf9,#f8f1e7)
                }
                .advisor-benefit strong{font-family:var(--font-display);font-size:22px;line-height:1.08}
                .advisor-benefit p{margin:0;color:var(--muted)}
                .advisor-surface{
                    padding:24px;border-radius:28px;background:linear-gradient(180deg,#fffdf8,#f7f0e4);
                    border:1px solid #ebe1cf;box-shadow:0 16px 34px rgba(48,54,36,.06)
                }
                .catalog-page{padding-bottom:54px}
                .catalog-hero-panel{
                    display:grid;grid-template-columns:minmax(0,.88fr) minmax(360px,1.12fr);
                    gap:24px;align-items:start
                }
                .catalog-hero-copy{display:grid;gap:16px;align-content:start;padding:8px 0 0}
                .catalog-hero-copy .content-prose h1{font-size:50px;max-width:13ch}
                .catalog-showcase{
                    display:grid;gap:14px;padding:20px;border-radius:28px;
                    border:1px solid rgba(223,210,188,.95);
                    background:linear-gradient(180deg,rgba(255,253,248,.98),rgba(247,239,226,.98));
                    box-shadow:0 14px 30px rgba(48,54,36,.06)
                }
                .catalog-showcase-head{display:grid;gap:8px}
                .catalog-showcase-head strong{font-family:var(--font-display);font-size:30px;line-height:1.06}
                .catalog-showcase-head p{margin:0;color:var(--muted)}
                .catalog-featured-product{
                    display:grid;grid-template-columns:118px minmax(0,1fr);gap:16px;align-items:center;
                    padding:16px;border-radius:20px;border:1px solid #e7dbc7;
                    background:linear-gradient(180deg,#fffefa,#f8f1e6)
                }
                .catalog-featured-media,.catalog-product-media{
                    display:grid;place-items:center;overflow:hidden;text-decoration:none;
                    background:radial-gradient(circle at top,#fffdf9,#eee3d1);
                    border:1px solid #e7dbc7
                }
                .catalog-featured-media{width:118px;aspect-ratio:1/1;border-radius:18px}
                .catalog-featured-media img,.catalog-product-media img{
                    width:100%;height:100%;object-fit:contain;
                    filter:drop-shadow(0 14px 20px rgba(31,52,40,.08))
                }
                .catalog-featured-copy{display:grid;gap:8px;min-width:0}
                .catalog-featured-copy strong{font-family:var(--font-display);font-size:24px;line-height:1.08}
                .catalog-featured-copy p{margin:0;color:var(--muted);font-size:14px;line-height:1.45}
                .catalog-featured-copy .card-actions{display:flex;gap:8px;flex-wrap:wrap}
                .catalog-featured-copy .button{padding:10px 12px;border-radius:14px;font-size:13px}
                .catalog-shop-section,.catalog-advisor-band{
                    margin:20px 0;padding:30px;border-radius:28px;
                    border:1px solid rgba(223,210,188,.9);background:rgba(255,253,249,.9);
                    box-shadow:var(--shadow)
                }
                .catalog-toolbar{
                    display:grid;grid-template-columns:minmax(260px,1fr) minmax(220px,300px);
                    gap:14px;align-items:end;margin:22px 0
                }
                .catalog-search,.catalog-sort{display:grid;gap:7px;color:var(--muted);font-size:13px;font-weight:700}
                .catalog-search input,.catalog-sort select{border-radius:16px;background:#fffdf9}
                .catalog-chip-row{grid-column:1/-1;display:flex;gap:9px;flex-wrap:wrap}
                .catalog-chip{
                    display:inline-flex;gap:7px;align-items:center;border:1px solid #e0d3bd;
                    background:#fffdf9;color:var(--ink);border-radius:999px;padding:9px 12px;
                    font:inherit;font-size:14px;font-weight:800;cursor:pointer
                }
                .catalog-chip span{color:var(--muted);font-weight:700}
                .catalog-chip.is-active{background:var(--accent-soft);border-color:#bfd9c0;color:var(--accent)}
                .catalog-result-line{display:flex;gap:7px;align-items:baseline;margin:0 0 14px;color:var(--muted)}
                .catalog-result-line strong{font-size:22px;color:var(--ink);font-family:var(--font-display)}
                .catalog-grid-shop{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
                .catalog-product-card{
                    display:grid;grid-template-rows:auto 1fr;min-width:0;border-radius:22px;
                    border:1px solid #e7dbc7;background:linear-gradient(180deg,#fffefa,#f8f1e6);
                    box-shadow:0 14px 30px rgba(48,54,36,.055);overflow:hidden
                }
                .catalog-product-card[hidden]{display:none}
                .catalog-product-media{aspect-ratio:1/1;border-width:0 0 1px;border-radius:0;padding:20px}
                .catalog-product-media span{color:var(--muted);font-weight:800}
                .catalog-product-body{display:grid;gap:11px;align-content:start;padding:18px}
                .catalog-product-body h3{
                    margin:0;font-family:var(--font-display);font-size:22px;line-height:1.08
                }
                .catalog-product-body h3 a{color:inherit;text-decoration:none}
                .catalog-product-body p{
                    margin:0;color:var(--muted);font-size:14px;line-height:1.48;
                    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden
                }
                .catalog-product-meta{
                    display:flex;justify-content:space-between;align-items:center;gap:10px;
                    color:var(--muted);font-size:13px
                }
                .catalog-price{font-weight:900;color:#244b36}
                .badge-ready{background:#e8f5ea;color:#2f6f4f}
                .badge-muted{background:#f2ebdd;color:#756345}
                .catalog-product-card .card-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:auto}
                .catalog-product-card .button{padding:10px 11px;border-radius:14px;font-size:13px}
                .catalog-empty-state{
                    margin:16px 0 0;padding:18px;border-radius:18px;background:#fff8eb;border:1px solid #ecdcc2;color:var(--muted)
                }
                .catalog-advisor-band{
                    display:grid;grid-template-columns:minmax(0,1fr) auto;gap:20px;align-items:center;
                    background:linear-gradient(135deg,#eff8f0,#fffaf1)
                }
                .catalog-advisor-band .content-prose h2{margin:6px 0 8px}
                .catalog-advisor-band .content-prose p{margin:0;color:var(--muted)}
                .article-catalog-hero-panel{
                    grid-template-columns:minmax(0,.94fr) minmax(500px,1.06fr);
                    gap:28px;align-items:stretch
                }
                .site-articles .catalog-hero-copy{
                    gap:18px;padding:10px 0;align-content:start
                }
                .site-articles .catalog-hero-copy .content-prose h1{font-size:46px;max-width:11ch}
                .article-hero-topics{
                    display:grid;gap:10px;margin-top:2px;padding:14px;border-radius:22px;
                    border:1px solid rgba(223,210,188,.88);
                    background:linear-gradient(180deg,rgba(255,253,248,.92),rgba(248,241,231,.92));
                    box-shadow:0 14px 30px rgba(48,54,36,.045)
                }
                .article-topic-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
                .article-topic-card{
                    appearance:none;border:1px solid #e7dbc7;border-radius:18px;background:#fffdf9;
                    color:var(--ink);display:grid;gap:5px;min-height:68px;padding:11px 12px;
                    text-align:left;font:inherit;cursor:pointer;transition:border-color .2s ease,background .2s ease,transform .2s ease
                }
                .article-topic-card:hover,.article-topic-card.is-active{
                    border-color:#b9d9c5;background:linear-gradient(180deg,#eef8f1,#fffdf8);
                    transform:translateY(-1px)
                }
                .article-topic-card strong{font-family:var(--font-display);font-size:18px;line-height:1.08}
                .article-topic-card span{color:var(--muted);font-size:12.5px;line-height:1.32}
                .article-catalog-showcase{
                    align-self:stretch;padding:20px;gap:11px;
                    background:linear-gradient(180deg,#fffdf9,#f8f1e6)
                }
                .article-catalog-showcase .catalog-showcase-head strong{
                    display:block;max-width:none;font-size:29px
                }
                .article-catalog-showcase .catalog-showcase-head p{font-size:15px;line-height:1.45}
                .article-featured-stack{display:grid;gap:12px}
                .catalog-featured-article{
                    box-shadow:0 10px 24px rgba(48,54,36,.04)
                }
                .catalog-featured-article.is-main{
                    grid-template-columns:minmax(154px,.58fr) minmax(0,1fr);
                    align-items:stretch;padding:13px
                }
                .catalog-featured-article.is-main .catalog-featured-media{
                    width:100%;height:100%;min-height:168px;aspect-ratio:auto;border-radius:18px
                }
                .catalog-featured-article.is-main .catalog-featured-copy{align-content:center;gap:8px}
                .catalog-featured-article.is-main .catalog-featured-copy strong{font-size:24px}
                .catalog-featured-article.is-secondary{
                    grid-template-columns:74px minmax(0,1fr);padding:11px;align-items:center
                }
                .catalog-featured-article.is-secondary .catalog-featured-media{
                    width:74px;aspect-ratio:1/1;border-radius:15px
                }
                .catalog-featured-article.is-secondary .catalog-featured-copy{gap:5px}
                .catalog-featured-article.is-secondary .catalog-featured-copy strong{font-size:18px}
                .catalog-featured-article.is-secondary .catalog-featured-copy p{display:none}
                .article-featured-link{
                    color:var(--accent);font-weight:900;text-decoration:none;font-size:14px
                }
                .catalog-featured-article-media img{
                    object-fit:cover;filter:brightness(.86) saturate(.92)
                }
                .article-catalog-grid{
                    display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px
                }
                .catalog-article-card{
                    display:grid;grid-template-rows:auto 1fr;min-width:0;border-radius:24px;
                    border:1px solid #e7dbc7;background:linear-gradient(180deg,#fffefa,#f8f1e6);
                    box-shadow:0 14px 30px rgba(48,54,36,.055);overflow:hidden
                }
                .catalog-article-card[hidden]{display:none}
                .catalog-article-media{
                    position:relative;display:block;aspect-ratio:16/10;overflow:hidden;
                    background:linear-gradient(135deg,#f4ecdf,#e8dfcd);text-decoration:none
                }
                .catalog-article-media img{
                    width:100%;height:100%;object-fit:cover;display:block;
                    filter:brightness(.82) saturate(.9);transition:transform .25s ease,filter .25s ease
                }
                .catalog-article-card:hover .catalog-article-media img{
                    transform:scale(1.025);filter:brightness(.9) saturate(.98)
                }
                .catalog-article-media > span:not(.badge){
                    display:grid;place-items:center;height:100%;color:var(--muted);font-weight:800;text-align:center;padding:18px
                }
                .catalog-article-media .badge{
                    position:absolute;left:14px;top:14px;background:rgba(255,248,235,.95);
                    color:#765b2e;box-shadow:0 8px 20px rgba(31,52,40,.08)
                }
                .catalog-article-body{display:grid;gap:10px;align-content:start;padding:18px}
                .catalog-article-body h3{
                    margin:0;font-family:var(--font-display);font-size:24px;line-height:1.05
                }
                .catalog-article-body h3 a{color:inherit;text-decoration:none}
                .catalog-article-body p{
                    margin:0;color:var(--muted);font-size:15px;line-height:1.5;
                    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden
                }
                .catalog-article-body .card-actions{margin-top:6px}
                .catalog-article-body .button{padding:11px 14px;border-radius:15px;font-size:14px}
                @media (max-width: 980px){
                    .layout{grid-template-columns:1fr}
                    .sidebar{position:static}
                    .metric-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
                    .card-grid,.split-hero,.step-grid,.conversion-box,.conversion-product-grid{grid-template-columns:1fr}
                    .home-hero-grid,.proof-bar,.authority-grid,.advisor-layout{grid-template-columns:1fr}
                    .trust-hero-panel,.trust-section-grid,.trust-cta-band{grid-template-columns:1fr}
                    .goal-hero-panel,.goal-advisor-band{grid-template-columns:1fr}
                    .goal-insight-grid,.goal-article-grid{grid-template-columns:1fr}
                    .home-hero-copy .content-prose h1{max-width:100%}
                    .catalog-hero-panel,.catalog-advisor-band{grid-template-columns:1fr}
                    .article-catalog-hero-panel{grid-template-columns:1fr}
                    .catalog-grid-shop{grid-template-columns:repeat(2,minmax(0,1fr))}
                    .article-catalog-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
                    .goal-product-grid{grid-template-columns:1fr}
                    .intent-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
                    .trust-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
                    .showcase-shortcuts{grid-template-columns:repeat(3,minmax(0,1fr))}
                    .goal-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
                    .showcase-feature{grid-template-columns:96px minmax(0,1fr)}
                    .advisor-input-row{grid-template-columns:1fr}
                    .product-hero-card{width:100%;max-width:560px;justify-self:center}
                    .support-cta-box{grid-template-columns:1fr}
                    .support-cta-box .card-actions{justify-content:flex-start}
                }
                @media (max-width: 640px){
                    body.site-public{padding-bottom:118px}
                    .shell{width:min(100% - 22px, 1160px)}
                    .hero-panel,.content-card,.sidebar-card{padding:22px}
                    .header-card{padding:16px 18px;border-radius:28px}
                    .brand-lockup{grid-template-columns:1fr}
                    .brand-logo{width:min(148px, 48vw)}
                    .brand-copy{gap:6px}
                    .content-prose h1,.home-hero-copy .content-prose h1{font-size:36px}
                    .trust-page{gap:16px;padding-top:8px}
                    .trust-hero-panel{padding:22px;border-radius:26px}
                    .trust-hero-copy h1{font-size:36px}
                    .trust-card-grid{grid-template-columns:1fr}
                    .trust-cta-band .trust-actions{display:grid}
                    .goal-page{gap:16px;padding-top:8px}
                    .goal-hero-panel,.goal-section{padding:22px;border-radius:26px}
                    .goal-hero-copy h1{font-size:36px}
                    .goal-product-card{grid-template-columns:86px minmax(0,1fr);padding:14px}
                    .goal-card-copy h3{font-size:22px}
                    .goal-card-copy .card-actions,.goal-advisor-band .trust-actions{display:grid}
                    .metric-grid{grid-template-columns:1fr}
                    .card-grid,.intent-grid,.showcase-shortcuts{grid-template-columns:1fr}
                    .catalog-hero-copy .content-prose h1{font-size:36px}
                    .catalog-shop-section,.catalog-advisor-band{padding:22px;border-radius:24px}
                    .catalog-toolbar,.catalog-grid-shop{grid-template-columns:1fr}
                    .article-catalog-grid{grid-template-columns:1fr}
                    .catalog-featured-product{grid-template-columns:1fr}
                    .catalog-featured-media{width:100%}
                    .catalog-featured-article.is-main,.catalog-featured-article.is-secondary{grid-template-columns:1fr}
                    .catalog-featured-article.is-main .catalog-featured-media,.catalog-featured-article.is-secondary .catalog-featured-media{
                        width:100%;aspect-ratio:16/9;min-height:0
                    }
                    .article-topic-grid{grid-template-columns:1fr}
                    .catalog-product-card .card-actions,.catalog-advisor-band .card-actions{display:grid}
                    .catalog-article-body h3{font-size:22px}
                    .goal-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
                    .goal-card{min-height:68px;padding:10px 11px}
                    .goal-card-title{font-size:18px}
                    .goal-card-action{font-size:11.5px}
                    .advisor-input-row,.advisor-action-row{grid-template-columns:1fr}
                    .advisor-message,.advisor-message.user{max-width:100%}
                    .discount-modal{padding:10px;align-items:end}
                    .discount-modal-card{max-height:94vh;border-radius:24px;padding:22px}
                    .discount-contact-grid,.discount-actions{grid-template-columns:1fr}
                    .hero-actions{display:grid}
                    .showcase-header{grid-template-columns:1fr}
                    .showcase-brand-logo{width:138px}
                    .showcase-feature{grid-template-columns:1fr}
                    .showcase-brand-copy strong,.showcase-visual-overlay strong,.advisor-benefit strong,.intent-card strong,.step-card strong{font-size:24px}
                    .proof-point strong,.authority-card h3{font-size:22px}
                    .conversion-actions{min-width:0}
                    .conversion-product-card .card-actions{display:grid}
                    .feature-row-media{grid-template-columns:64px minmax(0,1fr)}
                    .feature-thumb{width:64px}
                    .hero-copy .content-prose h1{font-size:36px}
                    .product-hero-media{min-height:220px}
                    .product-panel-row{grid-template-columns:16px minmax(0,1fr);padding:15px}
                    .product-panel-row strong{font-size:20px}
                    .faq-list{padding:20px;border-radius:22px}
                    .faq-list .content-prose h2{font-size:28px}
                    .faq-intro{font-size:15px}
                    .faq-item summary{grid-template-columns:minmax(0,1fr) 32px;padding:16px}
                    .faq-question-text{font-size:18px}
                    .faq-toggle{width:32px;height:32px}
                    .faq-answer{padding:0 16px 18px}
                    .support-cta-box{padding:20px;border-radius:22px}
                    .support-cta-box .card-actions,.inline-section .feature-actions-product{display:grid}
                    .inline-section .feature-row-media{grid-template-columns:72px minmax(0,1fr)}
                    .inline-section .feature-thumb{width:72px}
                    .sidebar-related-card .feature-row-media{grid-template-columns:66px minmax(0,1fr)}
                    .sidebar-related-card .feature-thumb{width:66px}
                    .inline-section .feature-row{align-items:start}
                    .inline-section .feature-row strong{font-size:21px}
                    .inline-section .feature-row .muted{font-size:15px}
                    .sidebar-related-card .feature-row strong{font-size:18px}
                    .sidebar-related-card .feature-actions{grid-template-columns:1fr}
                    .conversion-product-card .feature-thumb{width:100%}
                    .mobile-shop-bar{
                        position:fixed;left:10px;right:10px;bottom:10px;z-index:50;
                        display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:center;
                        padding:10px;border-radius:20px;border:1px solid rgba(223,210,188,.96);
                        background:rgba(255,253,249,.96);box-shadow:0 16px 44px rgba(31,52,40,.18);backdrop-filter:blur(12px)
                    }
                    .mobile-shop-copy{display:grid;gap:2px;min-width:0}
                    .mobile-shop-copy span{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--gold);font-weight:800}
                    .mobile-shop-copy strong{font-size:13px;line-height:1.15}
                    .mobile-shop-actions{display:flex;gap:8px}
                    .mobile-shop-actions .button{padding:10px 12px;border-radius:14px;font-size:13px}
                }
                @media (max-width: 360px){
                    .goal-grid{grid-template-columns:1fr}
                }
            </style>'
            . '</head><body' . ($bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') . '"' : '') . '>'
            . $body
            . $analyticsBody
            . '</body></html>';
    }

    private static function googleTagHead(array $options): string
    {
        if (($options['analytics_enabled'] ?? true) === false) {
            return '';
        }

        $tagId = trim((string) ($options['google_tag_id'] ?? getenv('AVC_GOOGLE_TAG_ID') ?: 'G-WPTBTHXN8H'));
        if ($tagId === '' || preg_match('/^G-[A-Z0-9]+$/', $tagId) !== 1) {
            return '';
        }

        $escapedTagId = htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8');

        return '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $escapedTagId . '"></script>'
            . '<script>'
            . 'window.dataLayer=window.dataLayer||[];'
            . 'function gtag(){dataLayer.push(arguments);}'
            . 'gtag("js",new Date());'
            . 'gtag("config","' . $escapedTagId . '");'
            . '</script>';
    }

    private static function analyticsBodyScript(array $options): string
    {
        if (($options['analytics_enabled'] ?? true) === false) {
            return '';
        }

        return '<script>'
            . '(function(){'
            . 'window.avcTrackEvent=function(name,params,callback){'
            . 'var done=false;'
            . 'var finish=function(){if(done)return;done=true;if(typeof callback==="function")callback();};'
            . 'var payload=Object.assign({page_path:window.location.pathname,page_language:document.documentElement.lang||""},params||{});'
            . 'if(typeof window.gtag==="function"){'
            . 'if(callback){payload.event_callback=finish;payload.event_timeout=900;}'
            . 'window.gtag("event",name,payload);'
            . 'if(callback)window.setTimeout(finish,950);'
            . '}else{finish();}'
            . '};'
            . 'document.addEventListener("click",function(event){'
            . 'var target=event.target;if(!(target instanceof Element))return;'
            . 'var link=target.closest("a[href]");if(!link)return;'
            . 'var url;try{url=new URL(link.getAttribute("href")||"",window.location.origin);}catch(error){return;}'
            . 'if(url.origin!==window.location.origin)return;'
            . 'if(url.pathname==="/go/discount"){'
            . 'window.avcTrackEvent("forever_outbound_click",{click_type:"discount_link",cta_position:"discount_link",destination_path:url.pathname,source_path:window.location.pathname});'
            . '}'
            . '},true);'
            . '})();'
            . '</script>';
    }
}
