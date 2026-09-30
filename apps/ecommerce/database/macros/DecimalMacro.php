<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class DecimalMacro
{
    /**
     * Add a decimal column to the table with dynamic parameters (precision and scale).
     *
     * @param Table $table
     * @param string $column
     * @param int|null $precision
     * @param int|null $scale
     * @param array<string, mixed> $options
     *
     * @return void
    */
    public static function add(
        Table $table,
        string $column,
        ?int $precision = null,
        ?int $scale = null,
        array $options = [],
    ): void {
        $defaults = [
            'notnull'   => false,
            'precision' => $precision ?? 10,
            'scale'     => $scale ?? 2,
            'default'   => 0.00,
        ];

        $options = array_merge($defaults, $options);

        $table->addColumn($column, 'decimal', $options);
    }
}
