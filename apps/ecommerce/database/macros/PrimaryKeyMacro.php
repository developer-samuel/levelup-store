<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class PrimaryKeyMacro
{
    /**
     * @param Table $table
     *
     * @return void
    */
    public static function add(Table $table): void
    {
        $table->setPrimaryKey(['id']);
    }
}
