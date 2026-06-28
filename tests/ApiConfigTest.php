<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\Tests;

use InvalidArgumentException;
use LoggaSynk\ConnectApi\Domain\ApiConfig;
use PHPUnit\Framework\TestCase;

final class ApiConfigTest extends TestCase
{
    public function testRejectsInvalidBaseUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ApiConfig::create('javascript:alert(1)');
    }

    public function testRejectsInvalidTimeout(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ApiConfig::create(timeoutInSeconds: 0);
    }
}
