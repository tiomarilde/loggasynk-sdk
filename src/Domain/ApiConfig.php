<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Domain;

final readonly class ApiConfig
{
    private const DEFAULT_BASE_URL = 'https://api.loggasynk.com.br/api/v1';

    private function __construct(
        public string $baseUrl,
        public int $timeoutInSeconds,
    ) {
    }

    public static function create(
        string $baseUrl = self::DEFAULT_BASE_URL,
        int $timeoutInSeconds = 15,
    ): self {
        return new self(rtrim($baseUrl, '/'), $timeoutInSeconds);
    }

    public function url(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
