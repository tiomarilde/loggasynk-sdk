<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Domain;

use InvalidArgumentException;

final readonly class ClientCredentials
{
    private function __construct(
        public string $clientId,
        public string $clientSecret,
    ) {
    }

    public static function create(string $clientId, string $clientSecret): self
    {
        return new self(
            self::required($clientId, 'clientId'),
            self::required($clientSecret, 'clientSecret'),
        );
    }

    /**
     * @return array{client_id: string, client_secret: string}
     */
    public function toPayload(): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
    }

    private static function required(string $value, string $field): string
    {
        $normalized = trim($value);

        if ($normalized !== '') {
            return $normalized;
        }

        throw new InvalidArgumentException("{$field} nao pode ser vazio.");
    }
}
