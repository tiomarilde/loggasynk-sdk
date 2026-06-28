<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Tests;

use LoggaSynk\ConnectApi\DTO\CallBlockingRequest;
use LoggaSynk\ConnectApi\DTO\CreateInstanceRequest;
use LoggaSynk\ConnectApi\DTO\SendImageMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendTextMessageRequest;
use LoggaSynk\ConnectApi\DTO\UpdateProfileRequest;
use LoggaSynk\ConnectApi\DTO\UpdateWebhookRequest;
use PHPUnit\Framework\TestCase;

final class DtoTest extends TestCase
{
    public function testConvertsDocumentedPayloadsToArrays(): void
    {
        self::assertSame(['name' => 'Suporte'], CreateInstanceRequest::create('Suporte')->toArray());
        self::assertSame(['reject_calls' => true], CallBlockingRequest::enabled()->toArray());
        self::assertSame(['webhook_url' => 'https://app.test/webhook'], UpdateWebhookRequest::create('https://app.test/webhook')->toArray());
        self::assertSame(['phone' => '5511999999999', 'message' => 'Ola!'], SendTextMessageRequest::create('5511999999999', 'Ola!')->toArray());
    }

    public function testOmitsOptionalNullFields(): void
    {
        self::assertSame(
            ['name' => 'Suporte'],
            UpdateProfileRequest::create(name: 'Suporte')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'image' => 'https://cdn.test/image.png'],
            SendImageMessageRequest::create('5511999999999', 'https://cdn.test/image.png')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'text' => 'Oi'],
            SendMessageRequest::create('5511999999999', text: 'Oi')->toArray(),
        );
    }
}
