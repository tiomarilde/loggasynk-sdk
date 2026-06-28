<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

final readonly class CallBlockingRequest implements RequestPayload
{
    private function __construct(
        public bool $rejectCalls,
    ) {
    }

    public static function enabled(): self
    {
        return new self(true);
    }

    public static function disabled(): self
    {
        return new self(false);
    }

    public function toArray(): array
    {
        return [
            'reject_calls' => $this->rejectCalls,
        ];
    }
}
