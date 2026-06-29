<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class ReactionRequest implements RequestPayload
{
    private function __construct(
        public string $phone,
        public string $messageId,
        public string $reaction,
    )
    {
    }

    public static function create(string $phone, string $messageId, string $reaction): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::requiredString($messageId, 'messageId'),
            trim($reaction),
        );
    }

    /**
     * Remove a reação de uma mensagem (envia reaction vazio).
     */
    public static function remove(string $phone, string $messageId): self
    {
        return self::create($phone, $messageId, '');
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'messageId' => $this->messageId,
            'reaction' => $this->reaction,
        ];
    }
}
