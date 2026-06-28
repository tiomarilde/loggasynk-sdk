<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Domain;

final readonly class WebhookSignatureVerifier
{
    private function __construct(
        private string $secret,
        private string $algorithm,
    ) {
    }

    public static function create(string $secret, string $algorithm = 'sha256'): self
    {
        return new self($secret, $algorithm);
    }

    public function isValid(string $rawBody, string $timestamp, string $signature): bool
    {
        $expected = "{$this->algorithm}=" . hash_hmac(
            $this->algorithm,
            "{$timestamp}.{$rawBody}",
            $this->secret,
        );

        return hash_equals($expected, $signature);
    }
}
