<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class CreateInstanceRequest implements RequestPayload
{
    private function __construct(
        public string $name,
    )
    {
    }

    public static function create(string $name): self
    {
        return new self(PayloadValidator::requiredString($name, 'name', 120));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
