<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

// Introspeccao e ciclo de vida da instancia.
$client = loggasynkClient();
$token = loggasynkToken($client);
$wa = $client->whatsapp();
$instanceId = requiredEnv('LOGGASYNK_INSTANCE_ID');

// Dados e configuracoes da instancia.
printJson('GET /whatsapp/{instanceId}/data', $wa->data($instanceId, $token));

// Status operacional + cobranca.
printJson('GET /whatsapp/{instanceId}/status', $wa->status($instanceId, $token));

// Dados do celular conectado (exige instancia conectada).
printJson('GET /whatsapp/{instanceId}/device', $wa->device($instanceId, $token));

// Reinicia a sessao reusando as credenciais salvas (reconecta sem novo QR).
printJson('POST /whatsapp/{instanceId}/restart', $wa->restart($instanceId, $token));

// Desconectar faz logout do numero; a reconexao exige ler um novo QR Code.
// Descomente para testar (operacao destrutiva).
// printJson('POST /whatsapp/{instanceId}/disconnect', $wa->disconnect($instanceId, $token));
