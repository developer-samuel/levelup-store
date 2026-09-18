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
    /**
     * @return string
    */
    public function getName(): string
    {
        return Types::DECIMAL;
    }

    /**
     * @param mixed[] $column
     * @param AbstractPlatform $platform
     *
     * @return string
    */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDecimalTypeDeclarationSQL($column);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return float|null
    */
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
