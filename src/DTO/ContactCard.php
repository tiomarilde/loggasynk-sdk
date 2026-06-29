<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class ContactCard
{
    private function __construct(
        public string  $fullName,
        public string  $phoneNumber,
        public ?string $organization,
    )
    {
    }

    public static function create(string $fullName, string $phoneNumber, ?string $organization = null): self
    {
        return new self(
            PayloadValidator::requiredString($fullName, 'fullName'),
            PayloadValidator::phone($phoneNumber),
            PayloadValidator::optionalString($organization, 'organization'),
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [
            'fullName' => $this->fullName,
            'phoneNumber' => $this->phoneNumber,
        ];

        if ($this->organization !== null) {
            $payload['organization'] = $this->organization;
        }

        return $payload;
    }
}
