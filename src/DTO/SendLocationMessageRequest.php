<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class SendLocationMessageRequest implements RequestPayload
{
    private function __construct(
        public string  $phone,
        public float   $latitude,
        public float   $longitude,
        public ?string $name,
        public ?string $address,
    )
    {
    }

    public static function create(
        string  $phone,
        float   $latitude,
        float   $longitude,
        ?string $name = null,
        ?string $address = null,
    ): self
    {
        return new self(
            PayloadValidator::phone($phone),
            PayloadValidator::coordinate($latitude, 'latitude', -90.0, 90.0),
            PayloadValidator::coordinate($longitude, 'longitude', -180.0, 180.0),
            PayloadValidator::optionalString($name, 'name'),
            PayloadValidator::optionalString($address, 'address'),
        );
    }

    public function toArray(): array
    {
        $payload = [
            'phone' => $this->phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->address !== null) {
            $payload['address'] = $this->address;
        }

        return $payload;
    }
}
