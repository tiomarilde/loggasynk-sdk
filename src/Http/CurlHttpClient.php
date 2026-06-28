<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Http;

use JsonException;
use LoggaSynk\ConnectApi\Exception\ApiException;

final readonly class CurlHttpClient implements HttpClient
{
    public function __construct(
        private int $timeoutInSeconds = 15,
    ) {
    }

    public function request(HttpRequest $request): HttpResponse
    {
        $handle = curl_init($request->url);

        if ($handle === false) {
            throw new ApiException('Nao foi possivel iniciar o cURL.');
        }

        curl_setopt_array($handle, $this->options($request));

        $rawBody = curl_exec($handle);
        $statusCode = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $error = curl_error($handle);

        curl_close($handle);

        if ($rawBody === false) {
            throw new ApiException("Falha de conexao com a API LoggaSynk: {$error}");
        }

        return new HttpResponse($statusCode, $this->decode((string) $rawBody));
    }

    /**
     * @return array<int, mixed>
     */
    private function options(HttpRequest $request): array
    {
        $headers = $request->headers;
        $body = $request->encodedBody();
        $options = [
            CURLOPT_CUSTOMREQUEST => $request->method->value,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutInSeconds,
            CURLOPT_CONNECTTIMEOUT => min(10, $this->timeoutInSeconds),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'loggasynk-connect-api-php/0.1',
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($body === null) {
            return $options;
        }

        $options[CURLOPT_HTTPHEADER] = [...$headers, 'Content-Type: application/json'];
        $options[CURLOPT_POSTFIELDS] = $body;

        return $options;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decode(string $body): ?array
    {
        if ($body === '') {
            return null;
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ApiException('Resposta JSON invalida da API LoggaSynk.', previous: $exception);
        }

        return is_array($decoded) ? $decoded : null;
    }
}
