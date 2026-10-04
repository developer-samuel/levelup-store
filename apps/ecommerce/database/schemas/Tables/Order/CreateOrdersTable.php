<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Order;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\BooleanMacro,
    Macros\DecimalMacro,
    Macros\EnumMacro,
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus
};

final class CreateOrdersTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('orders');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndex($table);
        self::addForeignKey($table);
        self::addCheckConstraints($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        self::addStandardColumn($table);
        self::addAdditionalColumns($table);
        self::addEnumColumns($table);
        self::addBooleanColumn($table);
        self::addTimestamps($table);
    }

    private static function addStandardColumn(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
    }

    private static function addAdditionalColumns(Table $table): void
    {
        StringMacro::string($table, 'code', 20);
        DecimalMacro::add($table, 'price');
    }

    private static function addEnumColumns(Table $table): void
    {
        EnumMacro::add(
            $table,
            'payment',
            OrderPaymentMethod::cases(),
            OrderPaymentMethod::CARD->value,
            10,
        );

        EnumMacro::add(
            $table,
            'status',
            OrderStatus::cases(),
            OrderStatus::PENDING->value,
            10,
        );
    }

    private static function addBooleanColumn(Table $table): void
    {
        BooleanMacro::add($table, 'send_shipping');
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['code'], 'unique_code_orders');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['user_id'], 'idx_orders_user_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'users', ['user_id'], ['id']);
    }

    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_code_length',
            'CHAR_LENGTH(code) = 2',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_price_positive',
            'price > 0',
        );
    }
}
