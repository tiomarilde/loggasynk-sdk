<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class UpdateProfileRequest implements RequestPayload
{
    private function __construct(
        public ?string $photoUrl,
        public ?string $name,
        public ?string $description,
    )
    {
    }

    public static function create(
        ?string $photoUrl = null,
        ?string $name = null,
        ?string $description = null,
    ): self
    {
        return new self(
            $photoUrl === null ? null : PayloadValidator::url($photoUrl, 'photoUrl'),
            PayloadValidator::optionalString($name, 'name', 120),
            PayloadValidator::optionalString($description, 'description', 512),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'photo_url' => $this->photoUrl,
            'name' => $this->name,
            'description' => $this->description,
        ], static fn(?string $value): bool => $value !== null);
    }
}
