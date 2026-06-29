<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class ReadMessageRequest implements RequestPayload
{
    private function __construct(
        public string $phone,
        public string $messageId,
    )
    {
    }

    public static function create(string $phone, string $messageId): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::requiredString($messageId, 'messageId'),
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'messageId' => $this->messageId,
        ];
    }
}
