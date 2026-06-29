<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\DTO\DeleteMessageRequest;
use LoggaSynk\ConnectApi\DTO\ForwardMessageRequest;
use LoggaSynk\ConnectApi\DTO\ReactionRequest;
use LoggaSynk\ConnectApi\DTO\ReadMessageRequest;
use LoggaSynk\ConnectApi\DTO\ReplyMessageRequest;

require __DIR__ . '/bootstrap.php';

// Interacoes sobre uma mensagem existente. O messageId vem do campo message_id
// do webhook de mensagem recebida (ou do retorno de um envio).
$client = loggasynkClient();
$token = loggasynkToken($client);
$wa = $client->whatsapp();
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');
$phone = requiredEnv('LOGGASYNK_MESSAGE_PHONE');
$messageId = requiredEnv('LOGGASYNK_MESSAGE_ID');

// Marca a mensagem recebida como lida (recibo de leitura).
printJson(
    'POST /whatsapp/{instanceId}/read',
    $wa->markAsRead($instanceId, ReadMessageRequest::create($phone, $messageId), $token),
);

// Reage com um emoji (string vazia remove a reacao).
printJson(
    'POST /whatsapp/{instanceId}/reaction',
    $wa->react($instanceId, ReactionRequest::create($phone, $messageId, "\u{1F44D}"), $token),
);

// Responde citando a mensagem. quotedText melhora a previa da citacao.
printJson(
    'POST /whatsapp/{instanceId}/reply',
    $wa->reply(
        $instanceId,
        ReplyMessageRequest::create($phone, 'Resposta automatica via SDK.', $messageId, quotedText: optionalEnv('LOGGASYNK_QUOTED_TEXT')),
        $token,
    ),
);

// Reencaminha a mensagem para outro numero (default: o mesmo phone).
printJson(
    'POST /whatsapp/{instanceId}/forward',
    $wa->forward(
        $instanceId,
        ForwardMessageRequest::create(optionalEnv('LOGGASYNK_FORWARD_PHONE', $phone) ?? $phone, $messageId),
        $token,
    ),
);

// Apaga a mensagem apenas para voce. Use DeleteMessageRequest::forEveryone(...)
// para revogar para todos (so funciona em mensagens enviadas por voce).
printJson(
    'DELETE /whatsapp/{instanceId}/message',
    $wa->deleteMessage($instanceId, DeleteMessageRequest::forMe($phone, $messageId), $token),
);
