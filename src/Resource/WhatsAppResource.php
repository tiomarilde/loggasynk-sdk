<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Resource;

use LoggaSynk\ConnectApi\Domain\AccessToken;
use LoggaSynk\ConnectApi\Domain\ApiConfig;
use LoggaSynk\ConnectApi\Domain\InstanceId;
use LoggaSynk\ConnectApi\DTO\RequestPayload;
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
    public function instances(array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->createInstance($payload, $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function createInstance(array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::post($this->config->url('/whatsapp/instances'), $this->payload($payload)),
            $token,
        );
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
        return $this->execute(HttpRequest::get($this->instanceWebhookUrl($instanceId)), $token);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function rename(string $instanceId, array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::patch($this->whatsappUrl($instanceId, 'name'), $this->payload($payload)),
            $token,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function updateProfile(string $instanceId, array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::post($this->whatsappUrl($instanceId, 'profile'), $this->payload($payload)),
            $token,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function updateWebhook(string $instanceId, array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::post($this->instanceWebhookUrl($instanceId), $this->payload($payload)),
            $token,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function configureCallBlocking(string $instanceId, array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::post($this->whatsappUrl($instanceId, 'call-blocking'), $this->payload($payload)),
            $token,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function sendText(string $instanceId, array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::post($this->whatsappUrl($instanceId, 'send-text'), $this->payload($payload)),
            $token,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function sendImage(string $instanceId, array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::post($this->whatsappUrl($instanceId, 'send-image'), $this->payload($payload)),
            $token,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    public function send(string $instanceId, array|RequestPayload $payload, AccessToken $token): ?array
    {
        return $this->execute(
            HttpRequest::post($this->whatsappUrl($instanceId, 'send'), $this->payload($payload)),
            $token,
        );
    }

    private function whatsappUrl(string $instanceId, string $path): string
    {
        return $this->config->url('/whatsapp/' . $this->instanceId($instanceId)->pathSegment() . "/{$path}");
    }

    private function instanceWebhookUrl(string $instanceId): string
    {
        return $this->config->url('/' . $this->instanceId($instanceId)->pathSegment() . '/webhook');
    }

    private function instanceId(string $instanceId): InstanceId
    {
        return InstanceId::fromString($instanceId);
    }

    /**
     * @param array<string, mixed>|RequestPayload $payload
     * @return array<string, mixed>
     */
    private function payload(array|RequestPayload $payload): array
    {
        return $payload instanceof RequestPayload ? $payload->toArray() : $payload;
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
