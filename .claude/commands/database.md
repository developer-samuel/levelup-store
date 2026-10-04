Create a new Doctrine migration for the ecommerce app.

The user will describe what tables or changes are needed. Generate the migration file and Schema Builder classes following project conventions.

## File naming

**Migration:** `apps/ecommerce/database/migrations/Version{NNN}_{PascalCaseName}.php`
**Schema Builder classes:** `apps/ecommerce/database/schemas/Tables/{Domain}/Create{TableName}Table.php`

Where `{NNN}` is the next available number (check existing files).

## Migration template

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\{
    Schemas\Tables\{Domain}\Create{TableOne}Table,
    Schemas\Tables\{Domain}\Create{TableTwo}Table
};

final class Version{NNN}_{PascalCaseName} extends AbstractMigration
{
    public function getDescription(): string
    {
        return '{Short description of what this migration creates/alters}';
    }

    public function up(Schema $schema): void
    {
        Create{TableOne}Table::build($schema);
        Create{TableTwo}Table::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('{table_two}');
        $schema->dropTable('{table_one}');
    }
}
```

## Schema Builder class template

```php
<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\{Domain};

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\IdMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro
};

final class Create{TableName}Table
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('{table_name}');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        // add domain columns here
    }
}
```

## Available Macros

All macros live in `apps/ecommerce/database/macros/`.

**ID columns** (`IdMacro`)

- `IdMacro::addIdColumn($table)` - INT auto-increment
- `IdMacro::addBigIdColumn($table)` - BIGINT auto-increment
- `IdMacro::addSmallIdColumn($table)` - SMALLINT auto-increment

**Primary key**

- `PrimaryKeyMacro::add($table)` - sets `id` as primary key

**String / Text** (`StringMacro`)

- `StringMacro::string($table, 'col', length, options[])` - VARCHAR (default 255)
- `StringMacro::text($table, 'col', options[])` - TEXT

**Numbers**

- `DecimalMacro::add($table, 'col', precision, scale, options[])` - DECIMAL (default 10,2)
- `BigIntegerMacro::unsignedBigInteger($table, 'col')` - unsigned BIGINT (e.g. for FK referencing a BIGINT primary key)
- `BigIntegerMacro::bigInteger($table, 'col')` - signed BIGINT
- `BigIntegerMacro::signedBigInteger($table, 'col')` - signed BIGINT (explicit)

**Date / Time** (`DateMacro`, `TimestampMacro`)

- `DateMacro::date($table, 'col')` - DATE (nullable)
- `DateMacro::datetime($table, 'col')` - DATETIME (nullable)
- `TimestampMacro::created($table)` - `created_at` NOT NULL
- `TimestampMacro::updated($table, notnull: false)` - `updated_at` (nullable by default)

**Boolean**

- `BooleanMacro::add($table, 'col', default: false)` - BOOLEAN

**Enum**

- `EnumMacro::add($table, 'col', MyEnum::cases(), default, length, nullable)` - custom ENUM column

**Indexes / Constraints**

- `UniqueKeyMacro::add($table, ['col'], 'index_name')` - unique index
- `IndexMacro::add($table, ['col'], 'index_name')` - regular index
- `ForeignKeyMacro::addForeignKeys($table, 'ref_table', ['col'], ['id'])` - FK (default: `ON DELETE CASCADE ON UPDATE CASCADE`)
- `CheckConstraintMacro::add($table, 'constraint_name', 'SQL expression')` - CHECK constraint

## Rules

1. **Never** write raw SQL in migrations - always create a Schema Builder class
2. Namespace for migrations: `DoctrineMigrations`
3. Namespace for Schema Builder classes: `Database\Schemas\Tables\{Domain}`
4. Use grouped `use` imports with `{}`
5. `down()` drops tables in reverse order of `up()` (child tables first), using `$schema->dropTable()`
6. FK constraints use `ON DELETE CASCADE` or `ON DELETE SET NULL` - choose based on domain logic
7. Countries table always exists first (Version001)
8. Group related tables in one migration file per domain

## After generating, run:

```bash
cd apps/ecommerce && bin/run php bin/console doctrine:migrations:migrate --no-interaction
```
