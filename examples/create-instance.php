<?php

declare(strict_types=1);

use LoggaSynk\ConnectApi\DTO\CreateInstanceRequest;

require __DIR__ . '/bootstrap.php';

$client = loggasynkClient();
$token = loggasynkToken($client);

printJson(
    'POST /whatsapp/instances',
    $client->whatsapp()->createInstance(
        CreateInstanceRequest::create(requiredEnv('LOGGASYNK_INSTANCE_NAME')),
        $token,
    ),
);
