<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class UniqueKeyMacro
{
    /** @param string[] $columns */
    public static function add(Table $table, array $columns, ?string $indexName = null): void
    {
        if ($indexName !== null) {
            $table->addUniqueIndex($columns, $indexName);
        } else {
            $table->addUniqueIndex($columns);
        }
    }
}
