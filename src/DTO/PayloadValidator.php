<?php

declare(strict_types=1);

namespace LoggaSynk\ConnectApi\DTO;

use InvalidArgumentException;

final class PayloadValidator
{
    private function __construct()
    {
    }

    public static function requiredString(string $value, string $field, int $maxLength = 255): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidArgumentException("{$field} nao pode ser vazio.");
        }

        if (strlen($normalized) > $maxLength) {
            throw new InvalidArgumentException("{$field} excede {$maxLength} caracteres.");
        }

        return $normalized;
    }

    public static function optionalString(?string $value, string $field, int $maxLength = 255): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::requiredString($value, $field, $maxLength);
    }

    public static function phone(string $value): string
    {
        $normalized = preg_replace('/\D+/', '', $value) ?? '';

        if (preg_match('/^\d{10,15}$/', $normalized) === 1) {
            return $normalized;
        }

        throw new InvalidArgumentException('phone deve conter DDI, DDD e numero, somente digitos.');
    }

    public static function url(string $value, string $field, bool $httpsOnly = false): string
    {
        $normalized = self::requiredString($value, $field, 2048);
        $scheme = parse_url($normalized, PHP_URL_SCHEME);

        if (!filter_var($normalized, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("{$field} deve ser uma URL valida.");
        }

        if ($httpsOnly && $scheme !== 'https') {
            throw new InvalidArgumentException("{$field} deve usar HTTPS.");
        }

        if (!$httpsOnly && !in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException("{$field} deve usar HTTP ou HTTPS.");
        }

        return $normalized;
    }
}
