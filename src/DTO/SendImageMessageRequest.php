<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendImageMessageRequest implements RequestPayload
{
    private function __construct(
        public string $phone,
        public string $image,
        public ?string $caption,
    ) {
    }

    public static function create(string $phone, string $image, ?string $caption = null): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::url($image, 'image'),
            PayloadValidator::optionalString($caption, 'caption', 1024),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'phone' => $this->phone,
            'image' => $this->image,
            'caption' => $this->caption,
        ], static fn (?string $value): bool => $value !== null);
    }
}
