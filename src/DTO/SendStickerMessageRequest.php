<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendStickerMessageRequest implements RequestPayload
{
    private function __construct(
        public string $phone,
        public string $sticker,
    )
    {
    }

    /**
     * @param string $sticker URL pública ou base64 da figura (WebP).
     */
    public static function create(string $phone, string $sticker): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::media($sticker, 'sticker'),
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'sticker' => $this->sticker,
        ];
    }
}
