# LoggaSynk Connect API

Conector PHP 8.4+ simples, reutilizavel e sem framework obrigatorio para a API LoggaSynk.

Base URL padrao:

```text
https://api.loggasynk.com.br/api/v1
```

## Instalar

Em desenvolvimento:

```bash
composer install
```

Em producao, sem PHPUnit e dependencias de teste:

```bash
composer install --no-dev --optimize-autoloader
```

Se for usar como dependencia em outro projeto:

```bash
composer require loggasynk/connect-api
```

## Configurar

Crie um `.env` no projeto ou injete as variaveis no ambiente da sua aplicacao:

```env
LOGGASYNK_CLIENT_ID=lgs_test_client_id
LOGGASYNK_CLIENT_SECRET=replace_with_client_secret
LOGGASYNK_INSTANCE_ID=inst_3f9a2c1b-7d4e-4a86-9b1f-2c5e8a0d6f12
```

## Cliente

```php
<?php

use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\LoggaSynkClient;

$client = LoggaSynkClient::create(
    ClientCredentials::create(
        $_ENV['LOGGASYNK_CLIENT_ID'],
        $_ENV['LOGGASYNK_CLIENT_SECRET'],
    ),
);

$token = $client->authenticate();
```

## Endpoints Disponiveis

| SDK | HTTP |
| --- | --- |
| `$client->authenticate()` | `POST /auth/token` |
| `$client->whatsapp()->createInstance(...)` | `POST /whatsapp/instances` |
| `$client->whatsapp()->instances(...)` | alias de `createInstance(...)` |
| `$client->whatsapp()->billing(...)` | `GET /whatsapp/{instanceId}/billing` |
| `$client->whatsapp()->status(...)` | `GET /whatsapp/{instanceId}/status` |
| `$client->whatsapp()->data(...)` | `GET /whatsapp/{instanceId}/data` |
| `$client->whatsapp()->device(...)` | `GET /whatsapp/{instanceId}/device` |
| `$client->whatsapp()->qrCode(...)` | `GET /whatsapp/{instanceId}/qr-code` |
| `$client->whatsapp()->restart(...)` | `POST /whatsapp/{instanceId}/restart` |
| `$client->whatsapp()->disconnect(...)` | `POST /whatsapp/{instanceId}/disconnect` |
| `$client->whatsapp()->updateProfile(...)` | `POST /whatsapp/{instanceId}/profile` |
| `$client->whatsapp()->configureCallBlocking(...)` | `POST /whatsapp/{instanceId}/call-blocking` |
| `$client->whatsapp()->rename(...)` | `PATCH /whatsapp/{instanceId}/name` |
| `$client->whatsapp()->webhook(...)` | `GET /{instanceId}/webhook` |
| `$client->whatsapp()->updateWebhook(...)` | `POST /{instanceId}/webhook` |
| `$client->whatsapp()->sendText(...)` | `POST /whatsapp/{instanceId}/send-text` |
| `$client->whatsapp()->sendImage(...)` | `POST /whatsapp/{instanceId}/send-image` |
| `$client->whatsapp()->sendAudio(...)` | `POST /whatsapp/{instanceId}/send-audio` |
| `$client->whatsapp()->sendVideo(...)` | `POST /whatsapp/{instanceId}/send-video` |
| `$client->whatsapp()->sendDocument(...)` | `POST /whatsapp/{instanceId}/send-document` |
| `$client->whatsapp()->sendSticker(...)` | `POST /whatsapp/{instanceId}/send-sticker` |
| `$client->whatsapp()->sendLocation(...)` | `POST /whatsapp/{instanceId}/send-location` |
| `$client->whatsapp()->sendContact(...)` | `POST /whatsapp/{instanceId}/send-contact` |
| `$client->whatsapp()->send(...)` | `POST /whatsapp/{instanceId}/send` |
| `$client->whatsapp()->reply(...)` | `POST /whatsapp/{instanceId}/reply` |
| `$client->whatsapp()->forward(...)` | `POST /whatsapp/{instanceId}/forward` |
| `$client->whatsapp()->react(...)` | `POST /whatsapp/{instanceId}/reaction` |
| `$client->whatsapp()->markAsRead(...)` | `POST /whatsapp/{instanceId}/read` |
| `$client->whatsapp()->deleteMessage(...)` | `DELETE /whatsapp/{instanceId}/message` |

## Autenticacao

```php
$token = $client->authenticate();

echo $token->authorizationHeader();
```

## Instancias WhatsApp

```php
use LoggaSynk\ConnectApi\DTO\CreateInstanceRequest;

$token = $client->authenticate();
$instanceId = 'inst_3f9a2c1b-7d4e-4a86-9b1f-2c5e8a0d6f12';

$instance = $client->whatsapp()->createInstance(
    CreateInstanceRequest::create('Suporte'),
    $token,
);

$status = $client->whatsapp()->status($instanceId, $token);
$data = $client->whatsapp()->data($instanceId, $token);
$billing = $client->whatsapp()->billing($instanceId, $token);
$qrCode = $client->whatsapp()->qrCode($instanceId, $token);
```

## Perfil E Configuracoes

```php
use LoggaSynk\ConnectApi\DTO\CallBlockingRequest;
use LoggaSynk\ConnectApi\DTO\RenameInstanceRequest;
use LoggaSynk\ConnectApi\DTO\UpdateProfileRequest;

$client->whatsapp()->updateProfile(
    $instanceId,
    UpdateProfileRequest::create(
        photoUrl: 'https://cdn.seusite.com/avatar.png',
        name: 'Suporte LoggaSynk',
        description: 'Atendimento das 9h as 18h.',
    ),
    $token,
);

$client->whatsapp()->configureCallBlocking(
    $instanceId,
    CallBlockingRequest::enabled(),
    $token,
);

$client->whatsapp()->rename(
    $instanceId,
    RenameInstanceRequest::create('Suporte'),
    $token,
);
```

## Mensagens

```php
use LoggaSynk\ConnectApi\DTO\SendImageMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendTextMessageRequest;

$client->whatsapp()->sendText(
    $instanceId,
    SendTextMessageRequest::create('5511999999999', 'Ola! Seu pedido foi aprovado.'),
    $token,
);

$client->whatsapp()->sendImage(
    $instanceId,
    SendImageMessageRequest::create(
        phone: '5511999999999',
        image: 'https://cdn.seusite.com/comprovante.png',
        caption: 'Segue o comprovante.',
    ),
    $token,
);

$client->whatsapp()->send(
    $instanceId,
    SendMessageRequest::create(
        phone: '5511999999999',
        text: 'Mensagem com imagem opcional',
        imageUrl: 'https://cdn.seusite.com/imagem.png',
    ),
    $token,
);
```

## Midia (audio, video, documento, sticker, localizacao, contato)

Toda midia aceita **URL publica** ou **base64**.

```php
use LoggaSynk\ConnectApi\DTO\ContactCard;
use LoggaSynk\ConnectApi\DTO\SendAudioMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendContactMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendDocumentMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendLocationMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendStickerMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendVideoMessageRequest;

$client->whatsapp()->sendAudio(
    $instanceId,
    SendAudioMessageRequest::create('5511999999999', 'https://cdn.seusite.com/audio.ogg', ptt: true),
    $token,
);

$client->whatsapp()->sendDocument(
    $instanceId,
    SendDocumentMessageRequest::create('5511999999999', 'https://cdn.seusite.com/contrato.pdf', fileName: 'Contrato.pdf'),
    $token,
);

$client->whatsapp()->sendLocation(
    $instanceId,
    SendLocationMessageRequest::create('5511999999999', -23.55052, -46.633308, name: 'Praca da Se'),
    $token,
);

$client->whatsapp()->sendContact(
    $instanceId,
    SendContactMessageRequest::create(
        '5511999999999',
        ContactCard::create('Maria Souza', '5511988887777', organization: 'Suporte'),
    ),
    $token,
);
```

## Interagir Com Mensagens (responder, reencaminhar, reagir, ler, apagar)

```php
use LoggaSynk\ConnectApi\DTO\DeleteMessageRequest;
use LoggaSynk\ConnectApi\DTO\ForwardMessageRequest;
use LoggaSynk\ConnectApi\DTO\ReactionRequest;
use LoggaSynk\ConnectApi\DTO\ReadMessageRequest;
use LoggaSynk\ConnectApi\DTO\ReplyMessageRequest;

// messageId vem do campo message_id do webhook de mensagem recebida.
$client->whatsapp()->reply(
    $instanceId,
    ReplyMessageRequest::create('5511999999999', 'Claro, posso ajudar!', $messageId, quotedText: 'Voces tem isso?'),
    $token,
);

$client->whatsapp()->react($instanceId, ReactionRequest::create('5511999999999', $messageId, '👍'), $token);
$client->whatsapp()->markAsRead($instanceId, ReadMessageRequest::create('5511999999999', $messageId), $token);
$client->whatsapp()->forward($instanceId, ForwardMessageRequest::create('5511888887777', $messageId), $token);
$client->whatsapp()->deleteMessage($instanceId, DeleteMessageRequest::forEveryone('5511999999999', $messageId), $token);
```

## Ciclo De Vida Da Instancia (reiniciar, desconectar, dispositivo)

```php
$device = $client->whatsapp()->device($instanceId, $token);      // dados do celular conectado
$client->whatsapp()->restart($instanceId, $token);               // reconecta sem novo QR
$client->whatsapp()->disconnect($instanceId, $token);            // logout; reconecta lendo novo QR
```

## Automacoes Da Instancia

```php
use LoggaSynk\ConnectApi\DTO\CallBlockingRequest;

// Recusar ligacoes + mensagem automatica + leitura automatica + visualizar status.
$client->whatsapp()->configureCallBlocking(
    $instanceId,
    CallBlockingRequest::create(
        rejectCalls: true,
        rejectMessage: 'Ola! Nao atendemos por ligacao. Mande sua mensagem aqui.',
        autoRead: true,
        autoReadStatus: true,
    ),
    $token,
);
```

## Webhook

```php
use LoggaSynk\ConnectApi\Domain\WebhookSignatureVerifier;
use LoggaSynk\ConnectApi\DTO\UpdateWebhookRequest;

$webhook = $client->whatsapp()->webhook($instanceId, $token);

$client->whatsapp()->updateWebhook(
    $instanceId,
    UpdateWebhookRequest::create('https://connect.bytebrix.com.br/webhooks/whatsapp'),
    $token,
);

$isValid = WebhookSignatureVerifier::create($secret)->isValid(
    $rawBody,
    $_SERVER['HTTP_X_LOGGASYNK_TIMESTAMP'] ?? '',
    $_SERVER['HTTP_X_LOGGASYNK_SIGNATURE'] ?? '',
);
```

## Testar Na API Real

Autentica contra `https://api.loggasynk.com.br`. Se `LOGGASYNK_INSTANCE_ID` estiver definido, tambem consulta `status` e `data`.

```bash
docker compose up -d
docker compose exec app composer api:smoke
```

O smoke test nao cria instancia, nao envia mensagem e nao altera configuracao.

## Exemplos Executaveis

Todos os exemplos usam as variaveis do `.env` quando executados via Docker Compose.

```bash
docker compose exec app composer auth:test
docker compose exec app composer api:smoke
docker compose exec app composer example:instance
docker compose exec app composer example:create-instance
docker compose exec app composer example:profile
docker compose exec app composer example:webhook
docker compose exec app composer example:messages
```

Observacoes:

- `example:instance` apenas consulta `status`, `data`, `billing` e `qr-code`.
- `example:create-instance` cria uma instancia e pode gerar fluxo de billing/PIX.
- `example:profile` altera perfil, bloqueio de ligacoes e nome da instancia.
- `example:webhook` consulta e atualiza a URL do webhook, alem de validar assinatura localmente.
- `example:messages` envia mensagens reais para `LOGGASYNK_MESSAGE_PHONE`.

## Design

- `Domain`: `ClientCredentials`, `AccessToken`, `ApiConfig` e verificacao de webhook.
- `DTO`: payloads tipados e validados antes da requisicao.
- `Http`: contrato `HttpClient`, request/response e adaptador `CurlHttpClient`.
- `Service`: casos de uso, como autenticacao.
- `Resource`: grupos de endpoints, como WhatsApp.
- `LoggaSynkClient` e uma fachada pequena para uso em qualquer projeto.
- IDs de instancia sao validados antes de entrar na URL.
- Webhooks usam HMAC com timestamp e tolerancia padrao de 5 minutos.

## Testes

Comandos de desenvolvimento do SDK:

```bash
composer install
composer check
composer test
composer auth:test
composer api:smoke
```

Para publicar/usar em producao, prefira:

```bash
composer install --no-dev --optimize-autoloader
```

## Seguranca

- DTOs validam campos obrigatorios, telefone, URLs e tamanhos antes da requisicao.
- `webhook_url` exige HTTPS.
- `instanceId` e validado antes de entrar na URL.
- Assinatura de webhook usa `timestamp.raw_body` com HMAC e tolerancia padrao de 5 minutos.
- O cliente cURL valida TLS e nao segue redirects automaticamente.
