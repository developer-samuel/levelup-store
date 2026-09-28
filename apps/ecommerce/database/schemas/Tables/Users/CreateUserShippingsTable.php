<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Users;

use Database\Schemas\Abstract\AbstractAddressTable;

final class CreateUserShippingsTable extends AbstractAddressTable
{
    /**
     * @return string
    */
    protected static function getTableName(): string
    {
        return 'user_shippings';
    }

    /**
     * @return string
    */
    protected static function getMainColumn(): string
    {
        return 'user_id';
    }

    /**
     * @return bool
    */
    protected static function withTimestamps(): bool
    {
        return true;
    }
}
