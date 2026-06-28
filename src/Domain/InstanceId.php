<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Domain;

use InvalidArgumentException;

final readonly class InstanceId
{
    private function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $normalized = trim($value);

        if (preg_match('/^inst_[A-Za-z0-9-]+$/', $normalized) === 1) {
            return new self($normalized);
        }

        throw new InvalidArgumentException('instanceId invalido.');
    }

    public function pathSegment(): string
    {
        return rawurlencode($this->value);
    }
}
