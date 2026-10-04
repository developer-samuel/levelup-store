<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class DateMacro
{
    public static function date(Table $table, string $name): void
    {
        $table->addColumn($name, 'date_immutable', [
            'notnull' => false,
        ]);
    }

    public static function datetime(Table $table, string $name): void
    {
        $table->addColumn($name, 'datetime_immutable', [
            'notnull' => false,
        ]);
    }
}
