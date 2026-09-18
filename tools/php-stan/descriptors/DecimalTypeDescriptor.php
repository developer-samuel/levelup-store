<?php

declare(strict_types=1);

namespace Tools\PhpStan\Descriptors;

use Doctrine\DBAL\Types\DecimalType;

use PHPStan\{
    Type\Doctrine\Descriptors\DoctrineTypeDescriptor,
    Type\FloatType,
    Type\Type
};

final class DecimalTypeDescriptor implements DoctrineTypeDescriptor
{
    /**
     * @return string
    */
    public function getType(): string
    {
        return DecimalType::class;
    }

    /**
     * @return Type
    */
    public function getWritableToPropertyType(): Type
    {
        return new FloatType();
    }

    /**
     * @return Type
    */
    public function getWritableToDatabaseType(): Type
    {
        return new FloatType();
    }

    /**
     * @return Type
    */
    public function getDatabaseInternalType(): Type
    {
        return new FloatType();
    }
}
