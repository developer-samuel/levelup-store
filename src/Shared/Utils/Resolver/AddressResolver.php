<?php

declare(strict_types=1);

namespace App\Shared\Utils\Resolver;

use App\Shared\{
    Enum\AddressFields,
    Enum\AddressType
};

final class AddressResolver
{
    /**
     * @param AddressType $type
     *
     * @return string[]
    */
    public static function for(AddressType $type): array
    {
        return array_map(
            static fn (AddressFields $field) => sprintf(
                '%s_%s',
                $type->value,
                $field->value,
            ),
            AddressFields::cases(),
        );
    }
}
