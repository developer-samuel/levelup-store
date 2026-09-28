<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class ForeignKeyMacro
{
    /**
     * Add predefined foreign keys to the table with dynamic parameters.
     *
     * @param Table $table
     * @param string $refTable
     * @param string[] $columns
     * @param string[] $refColumns
     * @param string[] $options
     *
     * @return void
     */
    public static function addForeignKeys(
        Table $table,
        string $refTable,
        array $columns,
        array $refColumns,
        array $options = [
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        ],
    ): void {
        $table->addForeignKeyConstraint(
            $refTable,
            $columns,
            $refColumns,
            $options,
        );
    }
}
