# LoggaSynk Connect API

Conector PHP 8.4+ simples, reutilizavel e sem framework obrigatorio para a API LoggaSynk.

## Instalar

```bash
composer install
```

## Autenticacao

```php
<?php

use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\DTO\SendTextMessageRequest;
use LoggaSynk\ConnectApi\LoggaSynkClient;

$client = LoggaSynkClient::create(
    ClientCredentials::create(
        $_ENV['LOGGASYNK_CLIENT_ID'],
        $_ENV['LOGGASYNK_CLIENT_SECRET'],
    ),
);

$token = $client->authenticate();

echo $token->authorizationHeader();
```

## WhatsApp

```php
$token = $client->authenticate();
$instanceId = 'inst_3f9a2c1b-7d4e-4a86-9b1f-2c5e8a0d6f12';

$instances = $client->whatsapp()->instances($token);
$status = $client->whatsapp()->status($instanceId, $token);

$client->whatsapp()->sendText(
    $instanceId,
    SendTextMessageRequest::create('5511999999999', 'Ola!'),
    $token,
);
```

## Webhook

```php
use LoggaSynk\ConnectApi\Domain\WebhookSignatureVerifier;

$isValid = WebhookSignatureVerifier::create($secret)->isValid(
    $rawBody,
    $_SERVER['HTTP_X_LOGGASYNK_TIMESTAMP'] ?? '',
    $_SERVER['HTTP_X_LOGGASYNK_SIGNATURE'] ?? '',
);
```

## Design

- `Domain`: `ClientCredentials`, `AccessToken`, `ApiConfig` e verificacao de webhook.
- `Http`: contrato `HttpClient`, request/response e adaptador `CurlHttpClient`.
- `Service`: casos de uso, como autenticacao.
- `Resource`: grupos de endpoints, como WhatsApp.
- `LoggaSynkClient` e uma fachada pequena para uso em qualquer projeto.

## Testes

```bash
composer test
composer auth:test
```
