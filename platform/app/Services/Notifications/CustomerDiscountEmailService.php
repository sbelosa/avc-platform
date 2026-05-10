<?php

declare(strict_types=1);

namespace Avc\Services\Notifications;

use Avc\Repositories\SettingsRepository;

final class CustomerDiscountEmailService
{
    public function __construct(private array $config)
    {
    }

    public function notify(array $lead, string $productDiscountUrl): bool
    {
        $recipientEmail = trim((string) ($lead['email'] ?? ''));
        if ($recipientEmail === '' || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $settings = (new SettingsRepository($this->config))->getReferralSettings();
        $languageCode = strtolower(trim((string) ($lead['language_code'] ?? 'hr'))) ?: 'hr';
        $alwaysDiscountUrl = $this->resolveAlwaysDiscountUrl($settings);
        $subject = $this->subject($languageCode);
        $body = $this->body($lead, $languageCode, $productDiscountUrl, $alwaysDiscountUrl, (int) ($settings['fcc_discount_percent'] ?? 15));
        $result = (new MailTransportService($this->config))->send(
            $recipientEmail,
            $subject,
            $body,
            $this->htmlBody($lead, $languageCode, $productDiscountUrl, $alwaysDiscountUrl, (int) ($settings['fcc_discount_percent'] ?? 15)),
            [
                'tags' => array_values(array_filter([
                    'avc_discount_link',
                    'avc_lang_' . $languageCode,
                    !empty($lead['discount_lead_id']) ? 'discount_lead_' . (int) $lead['discount_lead_id'] : null,
                ])),
            ]
        );

        return (bool) ($result['sent'] ?? false);
    }

    private function resolveAlwaysDiscountUrl(array $settings): string
    {
        $shortUrl = trim((string) ($settings['fcc_short_url'] ?? ''));
        if ($shortUrl !== '' && filter_var($shortUrl, FILTER_VALIDATE_URL)) {
            return $shortUrl;
        }

        $shortenUrl = trim((string) ($settings['fcc_shorten_url'] ?? ''));
        if ($shortenUrl !== '') {
            return 'https://' . ltrim($shortenUrl, '/');
        }

        return '';
    }

    private function subject(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Your 15% Forever discount link',
            'sl' => 'Tvoja povezava za 15% Forever popust',
            default => 'Tvoj link za 15% Forever popusta',
        };
    }

    private function body(array $lead, string $languageCode, string $productDiscountUrl, string $alwaysDiscountUrl, int $discountPercent): string
    {
        $name = trim((string) ($lead['name'] ?? ''));
        $productTitle = trim((string) ($lead['product_title'] ?? ''));
        $greetingName = $name !== '' ? ' ' . $name : '';

        if ($languageCode === 'en') {
            return implode(PHP_EOL, [
                'Hello' . $greetingName . ',',
                '',
                'thank you for your interest in Forever Living Products.',
                '',
                'Here is your personal link for ' . $discountPercent . '% off:',
                $alwaysDiscountUrl,
                '',
                $productTitle !== '' ? 'If you want to continue with the product you were viewing (' . $productTitle . '), use this link:' : 'If you want to continue with the product you were viewing, use this link:',
                $productDiscountUrl,
                '',
                'You can save this discount link and use it again for future orders whenever the Forever Card Club discount is available.',
                '',
                'The purchase is completed on the official Forever Living Products shop.',
                '',
                'Final prices, availability and checkout details are confirmed inside the official Forever shop.',
                '',
                'If you need help choosing, you can simply reply to this email.',
                '',
                'Aloe Vera Centar',
            ]);
        }

        if ($languageCode === 'sl') {
            return implode(PHP_EOL, [
                'Pozdrav' . $greetingName . ',',
                '',
                'hvala za zanimanje za Forever Living Products.',
                '',
                'Tukaj je tvoja osebna povezava za ' . $discountPercent . '% popusta:',
                $alwaysDiscountUrl,
                '',
                $productTitle !== '' ? 'Če želiš nadaljevati z izdelkom, ki si ga gledal/a (' . $productTitle . '), uporabi to povezavo:' : 'Če želiš nadaljevati z izdelkom, ki si ga gledal/a, uporabi to povezavo:',
                $productDiscountUrl,
                '',
                'Povezavo za popust lahko shraniš in jo znova uporabiš pri prihodnjih naročilih, kadar je Forever Card Club popust na voljo.',
                '',
                'Nakup zaključiš v uradnem Forever Living Products shopu.',
                '',
                'Končne cene, razpoložljivost in zaključek naročila potrdi uradni Forever shop.',
                '',
                'Če potrebuješ pomoč pri izbiri, lahko preprosto odgovoriš na ta email.',
                '',
                'Aloe Vera Centar',
            ]);
        }

        return implode(PHP_EOL, [
            'Pozdrav' . $greetingName . ',',
            '',
            'hvala na interesu za Forever Living Products.',
            '',
            'Ovo je tvoj osobni link za ' . $discountPercent . '% popusta:',
            $alwaysDiscountUrl,
            '',
            $productTitle !== '' ? 'Ako želiš nastaviti s proizvodom koji si gledao/la (' . $productTitle . '), koristi ovaj link:' : 'Ako želiš nastaviti s proizvodom koji si gledao/la, koristi ovaj link:',
            $productDiscountUrl,
            '',
            'Link za popust možeš spremiti i koristiti ponovno za buduće narudžbe kad god je Forever Card Club popust dostupan.',
            '',
            'Kupnju završavaš na službenom Forever Living Products shopu.',
            '',
            'Konačne cijene, dostupnost i završetak narudžbe potvrđuje službeni Forever shop.',
            '',
            'Ako trebaš pomoć oko odabira, možeš jednostavno odgovoriti na ovaj email.',
            '',
            'Aloe Vera Centar',
        ]);
    }

    private function htmlBody(array $lead, string $languageCode, string $productDiscountUrl, string $alwaysDiscountUrl, int $discountPercent): string
    {
        $name = trim((string) ($lead['name'] ?? ''));
        $productTitle = trim((string) ($lead['product_title'] ?? ''));
        $greetingName = $name !== '' ? ' ' . $name : '';
        $safeProductTitle = htmlspecialchars($productTitle, ENT_QUOTES, 'UTF-8');
        $safeAlwaysUrl = htmlspecialchars($alwaysDiscountUrl, ENT_QUOTES, 'UTF-8');
        $safeProductUrl = htmlspecialchars($productDiscountUrl, ENT_QUOTES, 'UTF-8');

        $copy = match ($languageCode) {
            'en' => [
                'hello' => 'Hello' . $greetingName . ',',
                'intro' => 'thank you for your interest in Forever Living Products.',
                'main' => 'Your personal ' . $discountPercent . '% discount link is ready:',
                'product' => $productTitle !== '' ? 'Continue with the product you were viewing: ' . $safeProductTitle : 'Continue with the product you were viewing:',
                'note' => 'You can save the discount link and use it again for future orders whenever the Forever Card Club discount is available.',
                'footer' => 'The purchase is completed on the official Forever Living Products shop. If you need help choosing, simply reply to this email.',
                'button_main' => 'Open my discount link',
                'button_product' => 'Continue with this product',
            ],
            'sl' => [
                'hello' => 'Pozdrav' . $greetingName . ',',
                'intro' => 'hvala za zanimanje za Forever Living Products.',
                'main' => 'Tvoja osebna povezava za ' . $discountPercent . '% popusta je pripravljena:',
                'product' => $productTitle !== '' ? 'Nadaljuj z izdelkom, ki si ga gledal/a: ' . $safeProductTitle : 'Nadaljuj z izdelkom, ki si ga gledal/a:',
                'note' => 'Povezavo lahko shraniš in jo znova uporabiš pri prihodnjih naročilih, kadar je Forever Card Club popust na voljo.',
                'footer' => 'Nakup zaključiš v uradnem Forever Living Products shopu. Če potrebuješ pomoč pri izbiri, odgovori na ta email.',
                'button_main' => 'Odpri povezavo za popust',
                'button_product' => 'Nadaljuj s tem izdelkom',
            ],
            default => [
                'hello' => 'Pozdrav' . $greetingName . ',',
                'intro' => 'hvala na interesu za Forever Living Products.',
                'main' => 'Tvoj osobni link za ' . $discountPercent . '% popusta je spreman:',
                'product' => $productTitle !== '' ? 'Nastavi s proizvodom koji si gledao/la: ' . $safeProductTitle : 'Nastavi s proizvodom koji si gledao/la:',
                'note' => 'Link možeš spremiti i koristiti ponovno za buduće narudžbe kad god je Forever Card Club popust dostupan.',
                'footer' => 'Kupnju završavaš na službenom Forever Living Products shopu. Ako trebaš pomoć oko odabira, samo odgovori na ovaj email.',
                'button_main' => 'Otvori link za popust',
                'button_product' => 'Nastavi s ovim proizvodom',
            ],
        };

        $mainButton = $alwaysDiscountUrl !== ''
            ? '<p style="margin:22px 0;"><a href="' . $safeAlwaysUrl . '" style="display:inline-block;background:#2f805c;color:#ffffff;text-decoration:none;font-weight:700;padding:13px 20px;border-radius:12px;">' . htmlspecialchars($copy['button_main'], ENT_QUOTES, 'UTF-8') . '</a></p>'
            : '';

        return '<div style="margin:0;padding:0;background:#f7f1e7;">'
            . '<div style="max-width:620px;margin:0 auto;padding:28px 18px;font-family:Arial,sans-serif;color:#17382a;">'
            . '<div style="background:#fffdf8;border:1px solid #ead9bd;border-radius:18px;padding:28px;">'
            . '<p style="margin:0 0 14px;font-size:18px;line-height:1.5;">' . htmlspecialchars($copy['hello'], ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p style="margin:0 0 18px;font-size:16px;line-height:1.65;">' . htmlspecialchars($copy['intro'], ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p style="margin:0;font-size:16px;line-height:1.65;">' . htmlspecialchars($copy['main'], ENT_QUOTES, 'UTF-8') . '</p>'
            . $mainButton
            . '<div style="margin:22px 0;padding:18px;border-radius:14px;background:#f5efe3;border:1px solid #ead9bd;">'
            . '<p style="margin:0 0 12px;font-size:16px;line-height:1.55;font-weight:700;">' . $copy['product'] . '</p>'
            . '<a href="' . $safeProductUrl . '" style="display:inline-block;background:#2f805c;color:#ffffff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:12px;">' . htmlspecialchars($copy['button_product'], ENT_QUOTES, 'UTF-8') . '</a>'
            . '</div>'
            . '<p style="margin:0 0 14px;font-size:15px;line-height:1.65;color:#5f7267;">' . htmlspecialchars($copy['note'], ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p style="margin:0;font-size:15px;line-height:1.65;color:#5f7267;">' . htmlspecialchars($copy['footer'], ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p style="margin:24px 0 0;font-weight:700;">Aloe Vera Centar</p>'
            . '</div></div></div>';
    }
}
