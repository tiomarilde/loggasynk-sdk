<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\DTO\ContactCard;
use LoggaSynk\ConnectApi\DTO\SendAudioMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendContactMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendDocumentMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendLocationMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendStickerMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendVideoMessageRequest;

require __DIR__ . '/bootstrap.php';

// Envio de midia: toda midia aceita URL publica ou base64 (com ou sem data URI).
$client = loggasynkClient();
$token = loggasynkToken($client);
$wa = $client->whatsapp();
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');
$phone = requiredEnv('LOGGASYNK_MESSAGE_PHONE');

// Audio: defina LOGGASYNK_AUDIO_URL para testar. ptt=true envia como mensagem de voz.
if (($audio = optionalEnv('LOGGASYNK_AUDIO_URL')) !== null) {
    printJson(
        'POST /whatsapp/{instanceId}/send-audio',
        $wa->sendAudio($instanceId, SendAudioMessageRequest::create($phone, $audio, ptt: true), $token),
    );
}

// Video: defina LOGGASYNK_VIDEO_URL para testar.
if (($video = optionalEnv('LOGGASYNK_VIDEO_URL')) !== null) {
    printJson(
        'POST /whatsapp/{instanceId}/send-video',
        $wa->sendVideo($instanceId, SendVideoMessageRequest::create($phone, $video, optionalEnv('LOGGASYNK_VIDEO_CAPTION')), $token),
    );
}

// Documento: defina LOGGASYNK_DOCUMENT_URL para testar.
if (($document = optionalEnv('LOGGASYNK_DOCUMENT_URL')) !== null) {
    printJson(
        'POST /whatsapp/{instanceId}/send-document',
        $wa->sendDocument(
            $instanceId,
            SendDocumentMessageRequest::create($phone, $document, optionalEnv('LOGGASYNK_DOCUMENT_NAME'), optionalEnv('LOGGASYNK_DOCUMENT_MIME')),
            $token,
        ),
    );
}

// Sticker (WebP): defina LOGGASYNK_STICKER_URL para testar.
if (($sticker = optionalEnv('LOGGASYNK_STICKER_URL')) !== null) {
    printJson(
        'POST /whatsapp/{instanceId}/send-sticker',
        $wa->sendSticker($instanceId, SendStickerMessageRequest::create($phone, $sticker), $token),
    );
}

// Localizacao (exemplo: Praca da Se).
printJson(
    'POST /whatsapp/{instanceId}/send-location',
    $wa->sendLocation(
        $instanceId,
        SendLocationMessageRequest::create($phone, -23.55052, -46.633308, 'Praca da Se', 'Sao Paulo - SP'),
        $token,
    ),
);

// Contato (vCard).
printJson(
    'POST /whatsapp/{instanceId}/send-contact',
    $wa->sendContact(
        $instanceId,
        SendContactMessageRequest::create($phone, ContactCard::create('Suporte LoggaSynk', $phone, 'LoggaSynk')),
        $token,
    ),
);
