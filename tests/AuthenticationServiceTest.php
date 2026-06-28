<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Tests;

use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\Exception\AuthenticationException;
use LoggaSynk\ConnectApi\Exception\InvalidPayloadException;
use LoggaSynk\ConnectApi\Exception\RateLimitException;
use LoggaSynk\ConnectApi\Http\HttpClient;
use LoggaSynk\ConnectApi\Http\HttpRequest;
use LoggaSynk\ConnectApi\Http\HttpResponse;
use LoggaSynk\ConnectApi\LoggaSynkClient;
use PHPUnit\Framework\TestCase;

final class AuthenticationServiceTest extends TestCase
{
    public function testAuthenticatesWithClientCredentials(): void
    {
        $httpClient = new FakeHttpClient(new HttpResponse(200, [
            'access_token' => 'fake_access_token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]));

        $client = LoggaSynkClient::create(
            ClientCredentials::create('lgs_test_client_id', 'replace_with_client_secret'),
            httpClient: $httpClient,
        );

        $token = $client->authenticate();

        self::assertSame('Bearer fake_access_token', $token->authorizationHeader());
        self::assertSame('POST', $httpClient->lastRequest?->method->value);
        self::assertSame('https://api.loggasynk.com.br/api/v1/auth/token', $httpClient->lastRequest?->url);
        self::assertSame([
            'client_id' => 'lgs_test_client_id',
            'client_secret' => 'replace_with_client_secret',
        ], $httpClient->lastRequest?->body);
    }

    public function testThrowsInvalidPayloadException(): void
    {
        $this->expectException(InvalidPayloadException::class);

        $this->clientForStatus(400)->authenticate();
    }

    public function testThrowsAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);

        $this->clientForStatus(401)->authenticate();
    }

    public function testThrowsRateLimitException(): void
    {
        $this->expectException(RateLimitException::class);

        $this->clientForStatus(429)->authenticate();
    }

    private function clientForStatus(int $statusCode): LoggaSynkClient
    {
        return LoggaSynkClient::create(
            ClientCredentials::create('lgs_test_client_id', 'replace_with_client_secret'),
            httpClient: new FakeHttpClient(new HttpResponse($statusCode, ['error' => 'erro'])),
        );
    }
}

final class FakeHttpClient implements HttpClient
{
    public ?HttpRequest $lastRequest = null;

    public function __construct(
        private readonly HttpResponse $response,
    ) {
    }

    public function request(HttpRequest $request): HttpResponse
    {
        $this->lastRequest = $request;

        return $this->response;
    }
}
