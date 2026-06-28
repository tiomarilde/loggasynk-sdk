<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Http;

use LoggaSynk\ConnectApi\Domain\AccessToken;

final readonly class HttpRequest
{
    /**
     * @param array<string, mixed>|null $body
     * @param list<string> $headers
     */
    private function __construct(
        public HttpMethod $method,
        public string $url,
        public ?array $body = null,
        public array $headers = [],
    ) {
    }

    /**
     * @param list<string> $headers
     */
    public static function get(string $url, array $headers = []): self
    {
        return new self(HttpMethod::Get, $url, headers: $headers);
    }

    /**
     * @param array<string, mixed> $body
     * @param list<string> $headers
     */
    public static function post(string $url, array $body = [], array $headers = []): self
    {
        return new self(HttpMethod::Post, $url, $body, $headers);
    }

    /**
     * @param array<string, mixed> $body
     * @param list<string> $headers
     */
    public static function patch(string $url, array $body = [], array $headers = []): self
    {
        return new self(HttpMethod::Patch, $url, $body, $headers);
    }

    public function withBearerToken(AccessToken $token): self
    {
        return new self(
            $this->method,
            $this->url,
            $this->body,
            [...$this->headers, 'Authorization: ' . $token->authorizationHeader()],
        );
    }

    public function encodedBody(): ?string
    {
        if ($this->body === null) {
            return null;
        }

        return json_encode($this->body, JSON_THROW_ON_ERROR);
    }
}
