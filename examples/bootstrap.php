<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\Domain\AccessToken;
use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\LoggaSynkClient;

require __DIR__ . '/../vendor/autoload.php';

function loggasynkClient(): LoggaSynkClient
{
    return LoggaSynkClient::create(
        ClientCredentials::create(
            requiredEnv('LOGGASYNK_CLIENT_ID'),
            requiredEnv('LOGGASYNK_CLIENT_SECRET'),
        ),
    );
}

function loggasynkToken(LoggaSynkClient $client): AccessToken
{
    return $client->authenticate();
}

function requiredEnv(string $name): string
{
    $value = getenv($name);

    if (is_string($value) && trim($value) !== '') {
        return $value;
    }

    fwrite(STDERR, "Defina {$name} no ambiente ou no .env do Docker Compose." . PHP_EOL);
    exit(1);
}

function optionalEnv(string $name, ?string $default = null): ?string
{
    $value = getenv($name);

    if (!is_string($value) || trim($value) === '') {
        return $default;
    }

    return $value;
}

function printJson(string $title, ?array $payload): void
{
    echo PHP_EOL . "== {$title} ==" . PHP_EOL;
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
}
