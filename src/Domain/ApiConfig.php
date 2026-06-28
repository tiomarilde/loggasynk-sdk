<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Domain;

use InvalidArgumentException;

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
        $normalizedBaseUrl = rtrim(trim($baseUrl), '/');
        $scheme = parse_url($normalizedBaseUrl, PHP_URL_SCHEME);

        if (!filter_var($normalizedBaseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('baseUrl deve ser uma URL valida.');
        }

        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('baseUrl deve usar HTTP ou HTTPS.');
        }

        if ($timeoutInSeconds < 1 || $timeoutInSeconds > 120) {
            throw new InvalidArgumentException('timeoutInSeconds deve estar entre 1 e 120.');
        }

        return new self($normalizedBaseUrl, $timeoutInSeconds);
    }

    public function url(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
