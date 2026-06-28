<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendTextMessageRequest implements RequestPayload
{
    private function __construct(
        public string $phone,
        public string $message,
    ) {
    }

    public static function create(string $phone, string $message): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::requiredString($message, 'message', 4096),
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'message' => $this->message,
        ];
    }
}
