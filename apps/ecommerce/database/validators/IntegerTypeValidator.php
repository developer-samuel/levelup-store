<?php

declare(strict_types=1);

namespace Database\Validators;

final class IntegerTypeValidator
{
    public static function validateIdType(string $type): void
    {
        $validTypes = [
            'integer',
            'bigint',
            'smallint',
        ];

        self::assertInAllowedValues('ID', $type, $validTypes);
    }

    /** @param array<mixed> $allowedValues */
    private static function assertInAllowedValues(string $type, mixed $value, array $allowedValues): void
    {
        if (!in_array($value, $allowedValues, true)) {
            $allowedString = self::allowedValuesToString($allowedValues);
            $valueString = self::toStringSafe($value);

            throw new \InvalidArgumentException(
                sprintf("Invalid %s '%s'. Supported values: '%s'.", $type, $valueString, $allowedString),
            );
        }
    }

    /** @param array<mixed> $values */
    private static function allowedValuesToString(array $values): string
    {
        return implode("', '", array_map(
            static fn(mixed $v): string => self::toStringSafe($v),
            $values),
        );
    }

    private static function toStringSafe(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        return var_export($value, true);
    }
}
