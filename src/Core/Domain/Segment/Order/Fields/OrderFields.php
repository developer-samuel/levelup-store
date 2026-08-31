<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Fields;

use App\Core\Domain\Segment\Order\Enum\OrderPersonalFields;

use App\Shared\{
    Enum\AddressType,
    Utils\Resolver\AddressResolver
};

final class OrderFields
{
    /**
     * @return string[]
    */
    public static function required(): array
    {
        return [
            ...array_map(
                static fn(OrderPersonalFields $f): string => $f->value,
                OrderPersonalFields::cases(),
            ),
            ...AddressResolver::for(AddressType::BILLING),
        ];
    }
}
