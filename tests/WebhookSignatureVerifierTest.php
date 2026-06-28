<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Tests;

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
            WebhookSignatureVerifier::create($secret)->isValid($rawBody, $timestamp, $signature),
        );
    }
}
