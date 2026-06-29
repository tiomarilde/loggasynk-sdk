<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\DTO\CallBlockingRequest;

require __DIR__ . '/bootstrap.php';

// Automacoes por instancia: recusa de ligacao + mensagem automatica, leitura
// automatica das mensagens recebidas e visualizacao automatica dos status.
$client = loggasynkClient();
$token = loggasynkToken($client);
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');

// Campos null sao omitidos (nao alteram o valor atual). rejectMessage vazio ("")
// limpa a mensagem automatica.
printJson(
    'POST /whatsapp/{instanceId}/call-blocking',
    $client->whatsapp()->configureCallBlocking(
        $instanceId,
        CallBlockingRequest::create(
            rejectCalls: true,
            rejectMessage: 'Ola! Nao atendemos por ligacao, mande sua mensagem aqui.',
            autoRead: true,
            autoReadStatus: true,
        ),
        $token,
    ),
);
