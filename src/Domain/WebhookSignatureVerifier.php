<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Domain;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final readonly class WebhookSignatureVerifier
{
    private const DEFAULT_TOLERANCE_IN_SECONDS = 300;

    private function __construct(
        private string $secret,
        private string $algorithm,
        private int $toleranceInSeconds,
    ) {
    }

    public static function create(
        string $secret,
        string $algorithm = 'sha256',
        int $toleranceInSeconds = self::DEFAULT_TOLERANCE_IN_SECONDS,
    ): self {
        $normalizedSecret = trim($secret);

        if ($normalizedSecret === '') {
            throw new InvalidArgumentException('secret nao pode ser vazio.');
        }

        if (!in_array($algorithm, hash_hmac_algos(), true)) {
            throw new InvalidArgumentException('algorithm nao suportado para HMAC.');
        }

        if ($toleranceInSeconds < 0) {
            throw new InvalidArgumentException('toleranceInSeconds nao pode ser negativo.');
        }

        return new self($normalizedSecret, $algorithm, $toleranceInSeconds);
    }

    public function isValid(
        string $rawBody,
        string $timestamp,
        string $signature,
        ?DateTimeImmutable $now = null,
    ): bool {
        if ($timestamp === '' || $signature === '') {
            return false;
        }

        if (!$this->isInsideTolerance($timestamp, $now ?? new DateTimeImmutable())) {
            return false;
        }

        return hash_equals($this->expectedSignature($rawBody, $timestamp), $signature);
    }

    private function expectedSignature(string $rawBody, string $timestamp): string
    {
        return "{$this->algorithm}=" . hash_hmac(
            $this->algorithm,
            "{$timestamp}.{$rawBody}",
            $this->secret,
        );
    }

    private function isInsideTolerance(string $timestamp, DateTimeImmutable $now): bool
    {
        if ($this->toleranceInSeconds === 0) {
            return true;
        }

        $eventTime = DateTimeImmutable::createFromFormat(DATE_ATOM, $timestamp)
            ?: DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', $timestamp, new DateTimeZone('UTC'))
            ?: false;

        if (!$eventTime instanceof DateTimeImmutable) {
            return false;
        }

        return abs($now->getTimestamp() - $eventTime->getTimestamp()) <= $this->toleranceInSeconds;
    }
}
