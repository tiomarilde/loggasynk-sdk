<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\LoggaSynkClient;

require __DIR__ . '/../vendor/autoload.php';

$clientId = getenv('LOGGASYNK_CLIENT_ID') ?: '';
$clientSecret = getenv('LOGGASYNK_CLIENT_SECRET') ?: '';
$instanceId = getenv('LOGGASYNK_INSTANCE_ID') ?: '';

$client = LoggaSynkClient::create(
    ClientCredentials::create($clientId, $clientSecret),
);

$token = $client->authenticate();

writeStep('auth/token', [
    'token_type' => $token->type,
    'expires_at' => $token->expiresAt()->format(DATE_ATOM),
    'authorization_header_preview' => substr($token->authorizationHeader(), 0, 24) . '...',
]);

if ($instanceId === '') {
    writeStep('whatsapp/status', [
        'skipped' => true,
        'reason' => 'Defina LOGGASYNK_INSTANCE_ID no .env para testar endpoints da instancia.',
    ]);

    exit(0);
}

writeStep('whatsapp/status', $client->whatsapp()->status($instanceId, $token));
writeStep('whatsapp/data', $client->whatsapp()->data($instanceId, $token));

/**
 * @param array<string, mixed>|null $payload
 */
function writeStep(string $name, ?array $payload): void
{
    echo PHP_EOL . "== {$name} ==" . PHP_EOL;
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
}
