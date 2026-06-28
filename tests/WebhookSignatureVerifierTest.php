<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Tests;

use DateTimeImmutable;
use InvalidArgumentException;
use LoggaSynk\ConnectApi\Domain\WebhookSignatureVerifier;
use PHPUnit\Framework\TestCase;

final class WebhookSignatureVerifierTest extends TestCase
{
    public function testValidatesTimestampedPayloadSignature(): void
    {
        $secret = 'secret';
        $timestamp = '2026-06-28T12:00:00-03:00';
        $rawBody = '{"event":"message.received"}';
        $signature = 'sha256=' . hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

        self::assertTrue(
            WebhookSignatureVerifier::create($secret)->isValid(
                $rawBody,
                $timestamp,
                $signature,
                new DateTimeImmutable('2026-06-28T12:01:00-03:00'),
            ),
        );
    }

    public function testRejectsExpiredTimestamp(): void
    {
        $secret = 'secret';
        $timestamp = '2026-06-28T12:00:00-03:00';
        $rawBody = '{"event":"message.received"}';
        $signature = 'sha256=' . hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

        self::assertFalse(
            WebhookSignatureVerifier::create($secret)->isValid(
                $rawBody,
                $timestamp,
                $signature,
                new DateTimeImmutable('2026-06-28T12:10:01-03:00'),
            ),
        );
    }

    public function testValidatesZuluTimestamp(): void
    {
        $secret = 'secret';
        $timestamp = '2026-06-28T15:00:00.000Z';
        $rawBody = '{"event":"message.received"}';
        $signature = 'sha256=' . hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

        self::assertTrue(
            WebhookSignatureVerifier::create($secret)->isValid(
                $rawBody,
                $timestamp,
                $signature,
                new DateTimeImmutable('2026-06-28T15:01:00+00:00'),
            ),
        );
    }

    public function testRejectsEmptySecret(): void
    {
        $this->expectException(InvalidArgumentException::class);

        WebhookSignatureVerifier::create('');
    }
}
