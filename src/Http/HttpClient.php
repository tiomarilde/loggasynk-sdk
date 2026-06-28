<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Http;

interface HttpClient
{
    public function request(HttpRequest $request): HttpResponse;
}
