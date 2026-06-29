<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Tests;

use LoggaSynk\ConnectApi\DTO\CallBlockingRequest;
use LoggaSynk\ConnectApi\DTO\ContactCard;
use LoggaSynk\ConnectApi\DTO\CreateInstanceRequest;
use LoggaSynk\ConnectApi\DTO\DeleteMessageRequest;
use LoggaSynk\ConnectApi\DTO\ForwardMessageRequest;
use LoggaSynk\ConnectApi\DTO\ReactionRequest;
use LoggaSynk\ConnectApi\DTO\ReadMessageRequest;
use LoggaSynk\ConnectApi\DTO\ReplyMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendAudioMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendContactMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendDocumentMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendImageMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendLocationMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendStickerMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendTextMessageRequest;
use LoggaSynk\ConnectApi\DTO\SendVideoMessageRequest;
use LoggaSynk\ConnectApi\DTO\UpdateProfileRequest;
use LoggaSynk\ConnectApi\DTO\UpdateWebhookRequest;
use InvalidArgumentException;
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

    public function testRejectsInvalidPhone(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SendTextMessageRequest::create('abc', 'Ola!');
    }

    public function testRejectsInsecureWebhookUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UpdateWebhookRequest::create('http://app.test/webhook');
    }

    public function testRejectsEmptyFlexibleMessage(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SendMessageRequest::create('5511999999999');
    }

    public function testConvertsMessageInteractionPayloads(): void
    {
        self::assertSame(
            ['phone' => '5511999999999', 'messageId' => '3A8F'],
            ReadMessageRequest::create('5511999999999', '3A8F')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'messageId' => '3A8F', 'reaction' => '👍'],
            ReactionRequest::create('5511999999999', '3A8F', '👍')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'messageId' => '3A8F', 'reaction' => ''],
            ReactionRequest::remove('5511999999999', '3A8F')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'message' => 'Claro!', 'messageId' => '3A8F', 'fromMe' => false],
            ReplyMessageRequest::create('5511999999999', 'Claro!', '3A8F')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'message' => 'Claro!', 'messageId' => '3A8F', 'fromMe' => false, 'quotedText' => 'Tem isso?'],
            ReplyMessageRequest::create('5511999999999', 'Claro!', '3A8F', 'Tem isso?')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511888887777', 'messageId' => '3A8F', 'fromPhone' => '5511999999999'],
            ForwardMessageRequest::create('5511888887777', '3A8F', '5511999999999')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'messageId' => '3A8F', 'forEveryone' => true, 'fromMe' => true],
            DeleteMessageRequest::forEveryone('5511999999999', '3A8F')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'messageId' => '3A8F', 'forEveryone' => false, 'fromMe' => true],
            DeleteMessageRequest::forMe('5511999999999', '3A8F')->toArray(),
        );
    }

    public function testConvertsMediaPayloads(): void
    {
        self::assertSame(
            ['phone' => '5511999999999', 'audio' => 'https://cdn.test/a.ogg', 'ptt' => true],
            SendAudioMessageRequest::create('5511999999999', 'https://cdn.test/a.ogg', ptt: true)->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'video' => 'https://cdn.test/v.mp4', 'caption' => 'Veja'],
            SendVideoMessageRequest::create('5511999999999', 'https://cdn.test/v.mp4', 'Veja')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'document' => 'https://cdn.test/d.pdf', 'fileName' => 'Doc.pdf'],
            SendDocumentMessageRequest::create('5511999999999', 'https://cdn.test/d.pdf', 'Doc.pdf')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'sticker' => 'https://cdn.test/s.webp'],
            SendStickerMessageRequest::create('5511999999999', 'https://cdn.test/s.webp')->toArray(),
        );

        self::assertSame(
            ['phone' => '5511999999999', 'latitude' => -23.55052, 'longitude' => -46.633308, 'name' => 'Se'],
            SendLocationMessageRequest::create('5511999999999', -23.55052, -46.633308, 'Se')->toArray(),
        );

        self::assertSame(
            [
                'phone' => '5511999999999',
                'contacts' => [
                    ['fullName' => 'Maria', 'phoneNumber' => '5511988887777', 'organization' => 'Suporte'],
                ],
            ],
            SendContactMessageRequest::create(
                '5511999999999',
                ContactCard::create('Maria', '5511988887777', 'Suporte'),
            )->toArray(),
        );
    }

    public function testCallBlockingCarriesAutomationFields(): void
    {
        self::assertSame(
            ['reject_calls' => true, 'reject_message' => 'Sem ligacao', 'auto_read' => true, 'auto_read_status' => false],
            CallBlockingRequest::create(true, 'Sem ligacao', true, false)->toArray(),
        );

        self::assertSame(['reject_calls' => false], CallBlockingRequest::disabled()->toArray());
    }

    public function testRejectsOutOfRangeLatitude(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SendLocationMessageRequest::create('5511999999999', 200.0, 0.0);
    }

    public function testRejectsEmptyContactList(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SendContactMessageRequest::create('5511999999999');
    }

    public function testRejectsEmptyMedia(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SendAudioMessageRequest::create('5511999999999', '   ');
    }
}
