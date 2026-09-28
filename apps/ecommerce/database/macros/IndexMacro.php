<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class IndexMacro
{
    /**
     * Add a single index to the table dynamically.
     *
     * @param Table $table
     * @param string[] $columns
     * @param string $indexName
     *
     * @return void
    */
    public static function add(Table $table, array $columns, string $indexName): void
    {
        $table->addIndex($columns, $indexName);
    }
}
