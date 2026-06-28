<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\DTO\SendImageMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendTextMessageRequest;

require __DIR__ . '/bootstrap.php';

$client = loggasynkClient();
$token = loggasynkToken($client);
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');
$phone = requiredEnv('LOGGASYNK_MESSAGE_PHONE');

printJson(
    'POST /whatsapp/{instanceId}/send-text',
    $client->whatsapp()->sendText(
        $instanceId,
        SendTextMessageRequest::create($phone, requiredEnv('LOGGASYNK_MESSAGE_TEXT')),
        $token,
    ),
);

printJson(
    'POST /whatsapp/{instanceId}/send-image',
    $client->whatsapp()->sendImage(
        $instanceId,
        SendImageMessageRequest::create(
            phone: $phone,
            image: requiredEnv('LOGGASYNK_IMAGE_URL'),
            caption: optionalEnv('LOGGASYNK_IMAGE_CAPTION'),
        ),
        $token,
    ),
);

printJson(
    'POST /whatsapp/{instanceId}/send',
    $client->whatsapp()->send(
        $instanceId,
        SendMessageRequest::create(
            phone: $phone,
            text: optionalEnv('LOGGASYNK_MESSAGE_TEXT'),
            imageUrl: optionalEnv('LOGGASYNK_IMAGE_URL'),
        ),
        $token,
    ),
);
