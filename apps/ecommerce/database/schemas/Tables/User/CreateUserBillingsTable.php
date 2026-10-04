<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\User;

use Database\Schemas\Abstract\AbstractAddressTable;

final class CreateUserBillingsTable extends AbstractAddressTable
{
    protected static function getTableName(): string
    {
        return 'user_billings';
    }

    protected static function getMainColumn(): string
    {
        return 'user_id';
    }

    protected static function withTimestamps(): bool
    {
        return true;
    }
}
