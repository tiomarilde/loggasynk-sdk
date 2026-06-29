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

    /**
     * Texto opcional que aceita string vazia (ex.: limpar um campo), apenas
     * limitando o tamanho. null mantém o campo inalterado.
     */
    public static function optionalText(?string $value, string $field, int $maxLength): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        if (strlen($normalized) > $maxLength) {
            throw new InvalidArgumentException("{$field} excede {$maxLength} caracteres.");
        }

        return $normalized;
    }

    /**
     * Mídia aceita como URL pública ou base64 (com ou sem data URI). Apenas
     * exige conteúdo; o formato é tratado pela API/bridge.
     */
    public static function media(string $value, string $field): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidArgumentException("{$field} nao pode ser vazio (envie uma URL publica ou base64).");
        }

        return $normalized;
    }

    public static function coordinate(float $value, string $field, float $min, float $max): float
    {
        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException("{$field} deve estar entre {$min} e {$max}.");
        }

        return $value;
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
