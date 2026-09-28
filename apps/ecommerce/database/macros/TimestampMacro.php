<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class TimestampMacro
{
    /**
     * Add created_at column.
     *
     * @param Table $table
     *
     * @return void
    */
    public static function created(Table $table): void
    {
        $table->addColumn('created_at', 'datetime_immutable');
    }

    /**
     * Add updated_at column.
     *
     * @param Table $table
     * @param bool $notnull
     *
     * @return void
    */
    public static function updated(Table $table, bool $notnull = false): void
    {
        $table->addColumn('updated_at', 'datetime_immutable', [
            'notnull' => $notnull,
        ]);
    }
}
