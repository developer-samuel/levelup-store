<?php

declare(strict_types=1);

namespace Database\Types;

use Doctrine\{
    DBAL\Platforms\AbstractPlatform,
    DBAL\Types\Type,
    DBAL\Types\Types
};

final class BigIntType extends Type
{
    public function getName(): string
    {
        return Types::BIGINT;
    }

    /** @param mixed[] $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBigIntTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if (!is_scalar($value)) {
            return null;
        }

        return (int) $value;
    }
}
