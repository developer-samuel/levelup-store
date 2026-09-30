<?php

declare(strict_types=1);

namespace Database\Validators;

final class IntegerTypeValidator
{
    /**
     * Validates if the given type is a valid column type for IDs.
     *
     * @param string $type
    */
    public static function validateIdType(string $type): void
    {
        $validTypes = [
            'integer',
            'bigint',
            'smallint',
        ];

        self::assertInAllowedValues('ID', $type, $validTypes);
    }

     /**
     * @param string $type
     * @param mixed $value
     * @param array<mixed> $allowedValues
     *
     * @return void
     *
     * @throws \InvalidArgumentException
    */
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

    /**
     * @param array<mixed> $values
     *
     * @return string
    */
    private static function allowedValuesToString(array $values): string
    {
        return implode("', '", array_map(
            static fn(mixed $v): string => self::toStringSafe($v),
            $values),
        );
    }

    /**
     * @param mixed $value
     *
     * @return string
    */
    private static function toStringSafe(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        return var_export($value, true);
    }
}
