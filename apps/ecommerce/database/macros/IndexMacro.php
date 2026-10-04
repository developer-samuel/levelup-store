<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class IndexMacro
{
    /** @param string[] $columns */
    public static function add(Table $table, array $columns, string $indexName): void
    {
        $table->addIndex($columns, $indexName);
    }
}
