<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Order;

use Database\Schemas\Abstract\AbstractAddressTable;

final class CreateOrderBillingsTable extends AbstractAddressTable
{
    protected static function getTableName(): string
    {
        return 'order_billings';
    }

    protected static function getMainColumn(): string
    {
        return 'order_id';
    }

    protected static function withTimestamps(): bool
    {
        return false;
    }
}
