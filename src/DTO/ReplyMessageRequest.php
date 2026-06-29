<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class ReplyMessageRequest implements RequestPayload
{
    private function __construct(
        public string  $phone,
        public string  $message,
        public string  $messageId,
        public ?string $quotedText,
        public bool    $fromMe,
    )
    {
    }

    public static function create(
        string  $phone,
        string  $message,
        string  $messageId,
        ?string $quotedText = null,
        bool    $fromMe = false,
    ): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::requiredString($message, 'message', 4096),
            PayloadValidator::requiredString($messageId, 'messageId'),
            PayloadValidator::optionalString($quotedText, 'quotedText', 4096),
            $fromMe,
        );
    }

    public function toArray(): array
    {
        $payload = [
            'phone' => $this->phone,
            'message' => $this->message,
            'messageId' => $this->messageId,
            'fromMe' => $this->fromMe,
        ];

        if ($this->quotedText !== null) {
            $payload['quotedText'] = $this->quotedText;
        }

        return $payload;
    }
}
