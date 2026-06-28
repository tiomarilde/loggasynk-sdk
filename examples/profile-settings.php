<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\DTO\CallBlockingRequest;
use LoggaSynk\ConnectApi\DTO\RenameInstanceRequest;
use LoggaSynk\ConnectApi\DTO\UpdateProfileRequest;

require __DIR__ . '/bootstrap.php';

$client = loggasynkClient();
$token = loggasynkToken($client);
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');

printJson(
    'POST /whatsapp/{instanceId}/profile',
    $client->whatsapp()->updateProfile(
        $instanceId,
        UpdateProfileRequest::create(
            photoUrl: optionalEnv('LOGGASYNK_PROFILE_PHOTO_URL'),
            name: optionalEnv('LOGGASYNK_PROFILE_NAME'),
            description: optionalEnv('LOGGASYNK_PROFILE_DESCRIPTION'),
        ),
        $token,
    ),
);

printJson(
    'POST /whatsapp/{instanceId}/call-blocking',
    $client->whatsapp()->configureCallBlocking($instanceId, CallBlockingRequest::enabled(), $token),
);

printJson(
    'PATCH /whatsapp/{instanceId}/name',
    $client->whatsapp()->rename(
        $instanceId,
        RenameInstanceRequest::create(requiredEnv('LOGGASYNK_INSTANCE_NAME')),
        $token,
    ),
);
