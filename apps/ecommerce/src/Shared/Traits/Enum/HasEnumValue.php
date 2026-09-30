<?php

declare(strict_types=1);

namespace App\Shared\Traits\Enum;

/**
 * @method static static[] cases()
*/
trait HasEnumValue
{
    /**
     * @return string[]
    */
    public static function values(): array
    {
        return array_map(
            static fn(\BackedEnum $case): string => $case->value,
            static::cases(),
        );
    }
}
