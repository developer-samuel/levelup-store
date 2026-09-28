<?php

declare(strict_types=1);

namespace Database\Schemas\Abstract;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\Macros\{
    IdMacro,
    Integer\BigIntegerMacro,
    Integer\IntegerMacro,
    PrimaryKeyMacro,
    StringMacro,
    TimestampMacro,
    IndexMacro,
    UniqueKeyMacro,
    ForeignKeyMacro,
    CheckConstraintMacro
};

abstract class AbstractAddressTable
{
    /**
     * @return string
    */
    abstract protected static function getTableName(): string;

    /**
     * @return string
    */
    abstract protected static function getMainColumn(): string;

    /**
     * @return bool
    */
    abstract protected static function withTimestamps(): bool;

    /**
     * @param Schema $schema
     *
     * @return void
    */
    final public static function build(Schema $schema): void
    {
        $table = $schema->createTable(static::getTableName());

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
        self::addCheckConstraints($table);
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    protected static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);

        self::addStandardColumns($table);
        self::addAdditionalColumns($table);

        if (static::withTimestamps()) {
            self::addTimestamps($table);
        }
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    protected static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, [static::getMainColumn()], 'unique_' . static::getMainColumn() . '_' . static::getTableName());
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    protected static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, [static::getMainColumn()], 'idx_' . static::getTableName() . '_' . static::getMainColumn());
        IndexMacro::add($table, ['country_id'], 'idx_' . static::getTableName() . '_country_id');
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    protected static function addForeignKeys(Table $table): void
    {
        $mainColumn = static::getMainColumn();

        $mainTable = rtrim($mainColumn, '_id') . 's';

        ForeignKeyMacro::addForeignKeys($table, $mainTable, [$mainColumn], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'countries', ['country_id'], ['id']);
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    protected static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_postal_code_min_length',
            'CHAR_LENGTH(postal_code) >= 3',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_city_min_length',
            'CHAR_LENGTH(city) >= 2',
        );
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    private static function addStandardColumns(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, static::getMainColumn());
        IntegerMacro::unsignedInteger(
            $table,
            'country_id',
            null,
            ['unsigned' => true, 'notnull' => false],
        );
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    private static function addAdditionalColumns(Table $table): void
    {
        StringMacro::string($table, 'street', 200);
        StringMacro::string($table, 'postal_code', 15);
        StringMacro::string($table, 'city', 100);
    }

    /**
     * @param Table $table
     *
     * @return void
    */
    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }
}
