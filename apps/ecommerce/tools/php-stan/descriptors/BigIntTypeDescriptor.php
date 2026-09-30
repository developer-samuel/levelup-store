<?php

declare(strict_types=1);

namespace Tools\PhpStan\Descriptors;

use Doctrine\DBAL\Types\BigIntType;

use PHPStan\{
    Type\Doctrine\Descriptors\DoctrineTypeDescriptor,
    Type\IntegerType,
    Type\Type
};

final class BigIntTypeDescriptor implements DoctrineTypeDescriptor
{
    /**
     * @return string
    */
    public function getType(): string
    {
        return BigIntType::class;
    }

    /**
     * @return Type
    */
    public function getWritableToPropertyType(): Type
    {
        return new IntegerType();
    }

    /**
     * @return Type
    */
    public function getWritableToDatabaseType(): Type
    {
        return new IntegerType();
    }

    /**
     * @return Type
    */
    public function getDatabaseInternalType(): Type
    {
        return new IntegerType();
    }
}
