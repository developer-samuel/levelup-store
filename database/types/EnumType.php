<?php

declare(strict_types=1);

namespace Database\Types;

use Doctrine\{
    DBAL\Platforms\AbstractPlatform,
    DBAL\Types\Type
};

final class EnumType extends Type
{
    /**
     * Returns the name of this type.
     *
     * @return string
    */
    public function getName(): string
    {
        return 'text';
    }

    /**
     * Converts PHP enum to database value (string).
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return string|null
    */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof \BackedEnum ? (string) $value->value : null;
    }

    /**
     * Converts database value to PHP enum (or raw value).
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
    */
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value;
    }

    /**
     * Gets the SQL declaration snippet for the enum type.
     *
     * @param array<string, int|string|null> $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
    */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => $fieldDeclaration['length'] ?? 255,
        ]);
    }

    /**
     * Requires a comment hint in the SQL for Doctrine to recognize this type.
     *
     * @param AbstractPlatform $platform
     *
     * @return bool
    */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
