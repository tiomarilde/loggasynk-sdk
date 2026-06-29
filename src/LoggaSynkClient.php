<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi;

use LoggaSynk\ConnectApi\Domain\AccessToken;
use LoggaSynk\ConnectApi\Domain\ApiConfig;
use LoggaSynk\ConnectApi\Domain\ClientCredentials;
use LoggaSynk\ConnectApi\Http\CurlHttpClient;
use LoggaSynk\ConnectApi\Http\HttpClient;
use LoggaSynk\ConnectApi\Resource\WhatsAppResource;
use LoggaSynk\ConnectApi\Service\AuthenticationService;

final readonly class LoggaSynkClient
{
    private function __construct(
        private AuthenticationService $authentication,
        private WhatsAppResource      $whatsapp,
    )
    {
    }

    public static function create(
        ClientCredentials $credentials,
        ?ApiConfig        $config = null,
        ?HttpClient       $httpClient = null,
    ): self
    {
        $resolvedConfig = $config ?? ApiConfig::create();
        $resolvedHttpClient = $httpClient ?? new CurlHttpClient($resolvedConfig->timeoutInSeconds);

        return new self(
            new AuthenticationService($resolvedConfig, $credentials, $resolvedHttpClient),
            new WhatsAppResource($resolvedConfig, $resolvedHttpClient),
        );
    }

    public function authenticate(): AccessToken
    {
        return $this->authentication->authenticate();
    }

    public function whatsapp(): WhatsAppResource
    {
        return $this->whatsapp;
    }
}
