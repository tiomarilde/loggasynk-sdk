<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class ForwardMessageRequest implements RequestPayload
{
    private function __construct(
        public string  $phone,
        public string  $messageId,
        public ?string $fromPhone,
    )
    {
    }

    public static function create(string $phone, string $messageId, ?string $fromPhone = null): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::requiredString($messageId, 'messageId'),
            $fromPhone === null ? null : PayloadValidator::phone($fromPhone),
        );
    }

    public function toArray(): array
    {
        $payload = [
            'phone' => $this->phone,
            'messageId' => $this->messageId,
        ];

        if ($this->fromPhone !== null) {
            $payload['fromPhone'] = $this->fromPhone;
        }

        return $payload;
    }
}
