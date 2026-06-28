<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class UpdateWebhookRequest implements RequestPayload
{
    private function __construct(
        public string $webhookUrl,
    ) {
    }

    public static function create(string $webhookUrl): self
    {
        return new self(PayloadValidator::url($webhookUrl, 'webhookUrl', httpsOnly: true));
    }

    public function toArray(): array
    {
        return [
            'webhook_url' => $this->webhookUrl,
        ];
    }
}
