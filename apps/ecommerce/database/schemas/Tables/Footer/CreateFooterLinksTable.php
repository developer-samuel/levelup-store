<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Footer;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\CheckConstraintMacro,
    Macros\EnumMacro,
    Macros\IdMacro,
    Macros\Integer\SmallIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro
};

use App\Core\Domain\Segment\Footer\Enum\FooterLinkGroup;
use App\Core\Domain\Segment\Footer\Enum\FooterLinkTarget;

final class CreateFooterLinksTable
{
    /**
     * Build the entire schema definition for the 'footer_links' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('footer_links');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addCheckConstraints($table);
    }

    /**
     * Add columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addColumns(Table $table): void
    {
        IdMacro::addSmallIdColumn($table);
        self::addAdditionalColumns($table);
        self::addEnumColumn($table);
        self::addTimestamps($table);
    }

    /**
     * Add additional columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addAdditionalColumns(Table $table): void
    {
        SmallIntegerMacro::unsignedSmallInteger($table, 'position');
        StringMacro::string($table, 'value', 100);
        StringMacro::string($table, 'image', 255, ['notnull' => false]);
        StringMacro::string($table, 'url', 255);
    }

    /**
     * Add enum column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table,
            'link_group',
            FooterLinkGroup::cases(),
            FooterLinkGroup::CONTACT->value,
            20,
        );

        EnumMacro::add(
            $table,
            'link_target',
            FooterLinkTarget::cases(),
            FooterLinkTarget::BLANK->value,
            10,
        );
    }

    /**
     * Add timestamp columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    /**
     * Add check constraints to ensure data validity.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_footer_links_value_min_length',
            'CHAR_LENGTH(value) >= 2',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_footer_links_url_min_length',
            'CHAR_LENGTH(url) >= 5',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_footer_links_image_min_length',
            'image IS NULL OR CHAR_LENGTH(image) >= 5',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_footer_links_position_positive',
            'position >= 1',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_footer_links_link_group_not_empty',
            'CHAR_LENGTH(link_group) >= 4',
        );
    }
}
