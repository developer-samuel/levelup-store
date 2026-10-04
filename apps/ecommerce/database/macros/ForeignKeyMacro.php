<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class ForeignKeyMacro
{
    /**
     * @param string[] $columns
     * @param string[] $refColumns
     * @param string[] $options
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
