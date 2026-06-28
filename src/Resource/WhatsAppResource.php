<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Resource;

use LoggaSynk\ConnectApi\Domain\AccessToken;
use LoggaSynk\ConnectApi\Domain\ApiConfig;
use LoggaSynk\ConnectApi\Exception\ApiHttpException;
use LoggaSynk\ConnectApi\Exception\AuthenticationException;
use LoggaSynk\ConnectApi\Exception\InvalidPayloadException;
use LoggaSynk\ConnectApi\Exception\RateLimitException;
use LoggaSynk\ConnectApi\Http\HttpClient;
use LoggaSynk\ConnectApi\Http\HttpRequest;
use LoggaSynk\ConnectApi\Http\HttpResponse;

final readonly class WhatsAppResource
{
    public function __construct(
        private ApiConfig $config,
        private HttpClient $httpClient,
    ) {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function instances(AccessToken $token): ?array
    {
        return $this->createInstance([], $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function createInstance(array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::post($this->config->url('/whatsapp/instances'), $payload), $token);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function billing(string $instanceId, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::get($this->whatsappUrl($instanceId, 'billing')), $token);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function qrCode(string $instanceId, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::get($this->whatsappUrl($instanceId, 'qr-code')), $token);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function status(string $instanceId, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::get($this->whatsappUrl($instanceId, 'status')), $token);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function data(string $instanceId, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::get($this->whatsappUrl($instanceId, 'data')), $token);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function webhook(string $instanceId, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::get($this->config->url("/{$instanceId}/webhook")), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function rename(string $instanceId, array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::patch($this->whatsappUrl($instanceId, 'name'), $payload), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function updateProfile(string $instanceId, array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::post($this->whatsappUrl($instanceId, 'profile'), $payload), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function updateWebhook(string $instanceId, array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::post($this->config->url("/{$instanceId}/webhook"), $payload), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function configureCallBlocking(string $instanceId, array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::post($this->whatsappUrl($instanceId, 'call-blocking'), $payload), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function sendText(string $instanceId, array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::post($this->whatsappUrl($instanceId, 'send-text'), $payload), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function sendImage(string $instanceId, array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::post($this->whatsappUrl($instanceId, 'send-image'), $payload), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function send(string $instanceId, array $payload, AccessToken $token): ?array
    {
        return $this->execute(HttpRequest::post($this->whatsappUrl($instanceId, 'send'), $payload), $token);
    }

    private function whatsappUrl(string $instanceId, string $path): string
    {
        return $this->config->url("/whatsapp/{$instanceId}/{$path}");
    }

    /**
     * @return array<string, mixed>|null
     */
    private function execute(HttpRequest $request, AccessToken $token): ?array
    {
        $response = $this->httpClient->request($request->withBearerToken($token));

        if ($response->statusCode >= 200 && $response->statusCode < 300) {
            return $response->body;
        }

        throw match ($response->statusCode) {
            400 => new InvalidPayloadException($response->statusCode, $response->body),
            401 => new AuthenticationException($response->statusCode, $response->body),
            429 => new RateLimitException($response->statusCode, $response->body),
            default => new ApiHttpException($response->statusCode, $response->body),
        };
    }
}
