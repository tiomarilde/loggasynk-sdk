<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Exception;

class ApiHttpException extends ApiException
{
    /**
     * @param array<string, mixed>|null $responseBody
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly ?array $responseBody,
    ) {
        parent::__construct("A API LoggaSynk retornou status {$statusCode}.");
    }
}
