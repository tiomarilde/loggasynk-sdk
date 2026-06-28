<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Tests;

use InvalidArgumentException;
use LoggaSynk\ConnectApi\Domain\AccessToken;
use LoggaSynk\ConnectApi\Domain\ApiConfig;
use LoggaSynk\ConnectApi\Http\HttpClient;
use LoggaSynk\ConnectApi\Http\HttpRequest;
use LoggaSynk\ConnectApi\Http\HttpResponse;
use LoggaSynk\ConnectApi\Resource\WhatsAppResource;
use PHPUnit\Framework\TestCase;

final class WhatsAppResourceTest extends TestCase
{
    public function testRejectsInvalidInstanceIdBeforeRequest(): void
    {
        $resource = new WhatsAppResource(ApiConfig::create(), new RecordingHttpClient());

        $this->expectException(InvalidArgumentException::class);

        $resource->status('../auth/token', $this->token());
    }

    private function token(): AccessToken
    {
        return AccessToken::fromArray([
            'access_token' => 'token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }
}

final class RecordingHttpClient implements HttpClient
{
    public ?HttpRequest $lastRequest = null;

    public function request(HttpRequest $request): HttpResponse
    {
        $this->lastRequest = $request;

        return new HttpResponse(200, ['success' => true]);
    }
}
