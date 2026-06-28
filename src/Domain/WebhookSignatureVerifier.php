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

    public function isValid(string $payload, string $signature): bool
    {
        $expected = hash_hmac($this->algorithm, $payload, $this->secret);
        $normalized = str_replace("{$this->algorithm}=", '', $signature);

        return hash_equals($expected, $normalized);
    }
}
