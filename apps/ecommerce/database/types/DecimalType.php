<?php

declare(strict_types=1);

namespace Database\Types;

use Doctrine\{
    DBAL\Platforms\AbstractPlatform,
    DBAL\Types\Type,
    DBAL\Types\Types
};

final class DecimalType extends Type
{
    public function getName(): string
    {
        return Types::DECIMAL;
    }

    /** @param mixed[] $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDecimalTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?float
    {
        if ($value === null) {
            return null;
        }

        if (!is_scalar($value)) {
            return null;
        }

        return (float) $value;
    }
}
