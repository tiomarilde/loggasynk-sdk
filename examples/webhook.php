<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\Domain\WebhookSignatureVerifier;
use LoggaSynk\ConnectApi\DTO\UpdateWebhookRequest;

require __DIR__ . '/bootstrap.php';

$client = loggasynkClient();
$token = loggasynkToken($client);
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');

printJson('GET /{instanceId}/webhook', $client->whatsapp()->webhook($instanceId, $token));

printJson(
    'POST /{instanceId}/webhook',
    $client->whatsapp()->updateWebhook(
        $instanceId,
        UpdateWebhookRequest::create(requiredEnv('LOGGASYNK_WEBHOOK_URL')),
        $token,
    ),
);

$rawBody = '{"event":"message.received"}';
$timestamp = gmdate('Y-m-d\TH:i:s.000\Z');
$secret = requiredEnv('LOGGASYNK_WEBHOOK_SIGNING_SECRET');
$signature = 'sha256=' . hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

echo PHP_EOL . '== local webhook signature verification ==' . PHP_EOL;
var_export(WebhookSignatureVerifier::create($secret)->isValid($rawBody, $timestamp, $signature));
echo PHP_EOL;
