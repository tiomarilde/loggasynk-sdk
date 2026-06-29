<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class DeleteMessageRequest implements RequestPayload
{
    private function __construct(
        public string $phone,
        public string $messageId,
        public bool   $forEveryone,
        public bool   $fromMe,
    )
    {
    }

    public static function create(
        string $phone,
        string $messageId,
        bool   $forEveryone = false,
        bool   $fromMe = true,
    ): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::requiredString($messageId, 'messageId'),
            $forEveryone,
            $fromMe,
        );
    }

    /**
     * Apaga a mensagem para todos (revoga). Exige ser uma mensagem sua.
     */
    public static function forEveryone(string $phone, string $messageId): self
    {
        return self::create($phone, $messageId, forEveryone: true, fromMe: true);
    }

    /**
     * Apaga a mensagem apenas para você.
     */
    public static function forMe(string $phone, string $messageId, bool $fromMe = true): self
    {
        return self::create($phone, $messageId, forEveryone: false, fromMe: $fromMe);
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'messageId' => $this->messageId,
            'forEveryone' => $this->forEveryone,
            'fromMe' => $this->fromMe,
        ];
    }
}
