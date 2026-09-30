<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class UniqueKeyMacro
{
    /**
     * Add a unique index to the table.
     *
     * @param Table $table
     * @param string[] $columns
     * @param string|null $indexName
     *
     * @return void
    */
    public static function add(Table $table, array $columns, ?string $indexName = null): void
    {
        if ($indexName !== null) {
            $table->addUniqueIndex($columns, $indexName);
        } else {
            $table->addUniqueIndex($columns);
        }
    }
}