<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

use InvalidArgumentException;

final readonly class SendContactMessageRequest implements RequestPayload
{
    public string $phone;

    /** @var list<ContactCard> */
    public array $contacts;

    private function __construct(string $phone, ContactCard ...$contacts)
    {
        if ($contacts === []) {
            throw new InvalidArgumentException('Informe ao menos um contato.');
        }

        $this->phone = $phone;
        $this->contacts = array_values($contacts);
    }

    public static function create(string $phone, ContactCard ...$contacts): self
    {
        return new self(PayloadValidator::phone($phone), ...$contacts);
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'contacts' => array_map(static fn (ContactCard $contact): array => $contact->toArray(), $this->contacts),
        ];
    }
}
