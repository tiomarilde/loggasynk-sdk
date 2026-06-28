<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\LoggaSynkClient;

require __DIR__ . '/../vendor/autoload.php';

$clientId = getenv('LOGGASYNK_CLIENT_ID') ?: '';
$clientSecret = getenv('LOGGASYNK_CLIENT_SECRET') ?: '';

$client = LoggaSynkClient::create(
    ClientCredentials::create($clientId, $clientSecret),
);

$token = $client->authenticate();

echo json_encode([
    'token_type' => $token->type,
    'expires_at' => $token->expiresAt()->format(DATE_ATOM),
    'authorization_header_preview' => substr($token->authorizationHeader(), 0, 24) . '...',
], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
