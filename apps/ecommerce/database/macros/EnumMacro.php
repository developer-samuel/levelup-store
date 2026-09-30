<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class EnumMacro
{
    /**
     * Add a Doctrine custom ENUM column.
     *
     * @param Table $table
     * @param string $column
     * @param \BackedEnum[] $enumCases
     * @param string|null $default
     * @param int $length
     * @param bool $nullable
     *
     * @return void
    */
    public static function add(
        Table $table,
        string $column,
        array $enumCases,
        ?string $default = null,
        int $length = 50,
        bool $nullable = false,
    ): void {
        $values = self::extractValues($enumCases);

        $table->addColumn($column, 'enum', [
            'length'  => $length,
            'default' => self::resolveDefaultValue($values, $default, $nullable),
            'notnull' => !$nullable,
            'comment' => self::buildEnumDefinition($values),
        ]);
    }

    /**
     * @param \BackedEnum[] $enumCases
     *
     * @return array<int, string|int>
    */
    private static function extractValues(array $enumCases): array
    {
        return array_values(array_map(
            static fn(\BackedEnum $case): string|int => $case->value,
            $enumCases,
        ));
    }

    /**
     * @param array<int, string|int> $values
     *
     * @return string
    */
    private static function buildEnumDefinition(array $values): string
    {
        return sprintf(
            'ENUM(%s)',
            implode(', ', array_map(
                static fn(string|int $val): string => sprintf("'%s'", $val),
                $values,
            )),
        );
    }

    /**
     * @param array<int, string|int> $values
     * @param string|null $default
     * @param bool $nullable
     *
     * @return string|null
    */
    private static function resolveDefaultValue(array $values, ?string $default, bool $nullable): ?string
    {
        if ($nullable) {
            return $default;
        }

        $finalDefault = $default ?? ($values[0] ?? null);

        return $finalDefault !== null ? (string) $finalDefault : null;
    }
}
