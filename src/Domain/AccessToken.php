<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Domain;

use DateMalformedStringException;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class AccessToken
{
    private function __construct(
        public string            $value,
        public string            $type,
        public int               $expiresIn,
        public DateTimeImmutable $issuedAt,
    )
    {
    }

    /**
     * @param array{access_token?: string, token_type?: string, expires_in?: int} $payload
     */
    public static function fromArray(array $payload): self
    {
        self::ensureString($payload, 'access_token');
        self::ensureString($payload, 'token_type');
        self::ensurePositiveInteger($payload, 'expires_in');

        return new self(
            $payload['access_token'],
            $payload['token_type'],
            $payload['expires_in'],
            new DateTimeImmutable(),
        );
    }

    public function authorizationHeader(): string
    {
        return "$this->type $this->value";
    }

    /**
     * @throws DateMalformedStringException
     */
    public function expiresAt(): DateTimeImmutable
    {
        return $this->issuedAt->modify("+$this->expiresIn seconds");
    }

    /**
     * @throws DateMalformedStringException
     */
    public function isExpired(?DateTimeImmutable $now = null): bool
    {
        return ($now ?? new DateTimeImmutable()) >= $this->expiresAt();
    }

    /**
     * @param array<string, mixed> $payload
     */
    private static function ensureString(array $payload, string $field): void
    {
        if (isset($payload[$field]) && is_string($payload[$field]) && trim($payload[$field]) !== '') {
            return;
        }

        throw new InvalidArgumentException("Campo $field ausente ou invalido.");
    }

    /**
     * @param array<string, mixed> $payload
     */
    private static function ensurePositiveInteger(array $payload, string $field): void
    {
        if (isset($payload[$field]) && is_int($payload[$field]) && $payload[$field] > 0) {
            return;
        }

        throw new InvalidArgumentException("Campo $field ausente ou invalido.");
    }
}
