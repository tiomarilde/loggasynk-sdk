<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendMessageRequest implements RequestPayload
{
    private function __construct(
        public string $phone,
        public ?string $text,
        public ?string $imageUrl,
    ) {
    }

    public static function create(
        string $phone,
        ?string $text = null,
        ?string $imageUrl = null,
    ): self {
        return new self($phone, $text, $imageUrl);
    }

    public function toArray(): array
    {
        return array_filter([
            'phone' => $this->phone,
            'text' => $this->text,
            'image_url' => $this->imageUrl,
        ], static fn (?string $value): bool => $value !== null);
    }
}
