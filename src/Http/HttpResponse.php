<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Http;

final readonly class HttpResponse
{
    /**
     * @param array<string, mixed>|null $body
     */
    public function __construct(
        public int $statusCode,
        public ?array $body,
    ) {
    }
}
