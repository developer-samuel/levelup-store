<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Cart\Enum;

use App\Shared\Traits\Enum\HasEnumLabel;

enum CartAction: string
{
    use HasEnumLabel;

    case ADD = 'add';
    case REMOVE = 'remove';

    /**
     * @return string
    */
    public function successMessage(): string
    {
        return match ($this) {
            self::ADD    => 'Product added to cart.',
            self::REMOVE => 'Product removed from cart.',
        };
    }
}
