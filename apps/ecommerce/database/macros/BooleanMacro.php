<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class BooleanMacro
{
    /**
     * @param Table $table
     * @param string $name
     * @param bool $default
     *
     * @return void
    */
    public static function add(Table $table, string $name, bool $default = false): void
    {
        $table->addColumn($name, 'boolean', [
            'default' => $default,
        ]);
    }
}
