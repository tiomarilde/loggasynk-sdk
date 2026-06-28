<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$client = loggasynkClient();
$token = loggasynkToken($client);
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');

printJson('GET /whatsapp/{instanceId}/status', $client->whatsapp()->status($instanceId, $token));
printJson('GET /whatsapp/{instanceId}/data', $client->whatsapp()->data($instanceId, $token));
printJson('GET /whatsapp/{instanceId}/billing', $client->whatsapp()->billing($instanceId, $token));
printJson('GET /whatsapp/{instanceId}/qr-code', $client->whatsapp()->qrCode($instanceId, $token));
