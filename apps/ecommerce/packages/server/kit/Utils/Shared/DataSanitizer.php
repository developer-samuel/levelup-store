<?php

declare(strict_types=1);

namespace Packages\Kit\Utils\Shared;

final class DataSanitizer
{
    public static function sanitizeInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        if (is_string($value)) {
            return self::intFromString($value);
        }

        return null;
    }

    public static function sanitizeFloat(mixed $value): ?float
    {
        if (is_float($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            return self::floatFromString($value);
        }

        return null;
    }

    public static function sanitizeBoolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public static function sanitizeString(mixed $value): string
    {
        if (is_string($value)) {
            return trim($value);
        }

        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        return '';
    }

    /** @return array<int|string, mixed> */
    public static function sanitizeArray(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_filter(
            $value,
            static fn(mixed $item): bool => $item !== null,
        );
    }

    /** @return string[] */
    public static function sanitizeStringArray(mixed $values): array
    {
        if (!is_array($values)) {
            return [];
        }

        return array_map(
            static fn($v): string => is_scalar($v) || $v === null
                ? trim((string) $v)
                : trim(var_export($v, true)),
            $values,
        );
    }

    private static function intFromString(string $value): ?int
    {
        $trim = trim($value);

        if ($trim === '') {
            return null;
        }

        if (ctype_digit($trim)) {
            return (int) $trim;
        }

        if (preg_match('/^[+-]?\d+$/', $trim) === 1) {
            return (int) $trim;
        }

        return null;
    }

    private static function floatFromString(string $value): ?float
    {
        $trim = trim($value);
        if ($trim === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $trim);

        if (is_numeric($normalized)) {
            return (float) $normalized;
        }

        return null;
    }
}
