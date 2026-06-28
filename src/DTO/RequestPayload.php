<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

interface RequestPayload
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
