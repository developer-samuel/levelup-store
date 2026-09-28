<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Orders;

use Database\Schemas\Abstract\AbstractAddressTable;

final class CreateOrderShippingsTable extends AbstractAddressTable
{
    /**
     * @return string
    */
    protected static function getTableName(): string
    {
        return 'order_shippings';
    }

    /**
     * @return string
    */
    protected static function getMainColumn(): string
    {
        return 'order_id';
    }

    /**
     * @return bool
    */
    protected static function withTimestamps(): bool
    {
        return false;
    }
}
