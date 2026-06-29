<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Service;

use LoggaSynk\ConnectApi\Domain\AccessToken;
use LoggaSynk\ConnectApi\Domain\ApiConfig;
use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\Exception\ApiHttpException;
use LoggaSynk\ConnectApi\Exception\AuthenticationException;
use LoggaSynk\ConnectApi\Exception\InvalidPayloadException;
use LoggaSynk\ConnectApi\Exception\RateLimitException;
use LoggaSynk\ConnectApi\Http\HttpClient;
use LoggaSynk\ConnectApi\Http\HttpRequest;
use LoggaSynk\ConnectApi\Http\HttpResponse;

final readonly class AuthenticationService
{
    public function __construct(
        private ApiConfig         $config,
        private ClientCredentials $credentials,
        private HttpClient        $httpClient,
    )
    {
    }

    public function authenticate(): AccessToken
    {
        $response = $this->httpClient->request(
            HttpRequest::post(
                $this->config->url('/auth/token'),
                $this->credentials->toPayload(),
            ),
        );

        $this->ensureSuccessful($response);

        return AccessToken::fromArray($response->body ?? []);
    }

    private function ensureSuccessful(HttpResponse $response): void
    {
        if ($response->statusCode === 200) {
            return;
        }

        throw match ($response->statusCode) {
            400 => new InvalidPayloadException($response->statusCode, $response->body),
            401 => new AuthenticationException($response->statusCode, $response->body),
            429 => new RateLimitException($response->statusCode, $response->body),
            default => new ApiHttpException($response->statusCode, $response->body),
        };
    }
}
