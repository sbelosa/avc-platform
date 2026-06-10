<?php

declare(strict_types=1);

use Avc\Repositories\DiscountLeadRepository;
use Avc\Services\Notifications\CustomerDiscountEmailService;

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
$repository = new DiscountLeadRepository($config);

$leadId = 0;
$limit = 50;
foreach (array_slice($argv, 1) as $argument) {
    if (str_starts_with($argument, '--lead-id=')) {
        $leadId = (int) substr($argument, strlen('--lead-id='));
    }

    if (str_starts_with($argument, '--limit=')) {
        $limit = (int) substr($argument, strlen('--limit='));
    }
}

$leads = $leadId > 0
    ? array_values(array_filter([$repository->findForCustomerNotification($leadId)]))
    : $repository->pendingCustomerNotifications($limit);

$sent = 0;
$failed = 0;
$service = new CustomerDiscountEmailService($config);

foreach ($leads as $lead) {
    $email = trim((string) ($lead['email'] ?? ''));
    if ($email === '') {
        continue;
    }

    $discountRedirectUrl = rtrim((string) ($config['base_url'] ?? 'https://aloavera-centar.com'), '/')
        . '/go/discount?token=' . rawurlencode((string) ($lead['discount_token'] ?? ''));

    $ok = $service->notify([
        'discount_lead_id' => (int) ($lead['discount_lead_id'] ?? 0),
        'name' => (string) ($lead['name'] ?? ''),
        'email' => $email,
        'language_code' => (string) ($lead['language_code'] ?? 'hr'),
        'product_title' => (string) ($lead['product_title'] ?? ''),
    ], $discountRedirectUrl);

    if ($ok) {
        $repository->markCustomerNotified((int) $lead['discount_lead_id']);
        $sent++;
        echo 'sent discount_lead_id=' . (int) $lead['discount_lead_id'] . ' email=' . $email . PHP_EOL;
    } else {
        $failed++;
        echo 'failed discount_lead_id=' . (int) $lead['discount_lead_id'] . ' email=' . $email . PHP_EOL;
    }
}

echo json_encode([
    'checked' => count($leads),
    'sent' => $sent,
    'failed' => $failed,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
