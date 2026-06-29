<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendMessageRequest implements RequestPayload
{
    private function __construct(
        public string  $phone,
        public ?string $text,
        public ?string $imageUrl,
    )
    {
    }

    public static function create(
        string  $phone,
        ?string $text = null,
        ?string $imageUrl = null,
    ): self
    {
        if ($text === null && $imageUrl === null) {
            throw new \InvalidArgumentException('Informe text ou imageUrl.');
        }

        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::optionalString($text, 'text', 4096),
            $imageUrl === null ? null : PayloadValidator::url($imageUrl, 'imageUrl'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'phone' => $this->phone,
            'text' => $this->text,
            'image_url' => $this->imageUrl,
        ], static fn(?string $value): bool => $value !== null);
    }
}
