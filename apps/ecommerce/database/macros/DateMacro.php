<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class DateMacro
{
    /**
     * Add a date column with a given name.
     */
    public static function date(Table $table, string $name): void
    {
        $table->addColumn($name, 'date_immutable', [
            'notnull' => false,
        ]);
    }

    /**
     * Add a datetime column with a given name.
     *
     * @param Table $table
     * @param string $name
     *
     * @return void
    */
    public static function datetime(Table $table, string $name): void
    {
        $table->addColumn($name, 'datetime_immutable', [
            'notnull' => false,
        ]);
    }
}
