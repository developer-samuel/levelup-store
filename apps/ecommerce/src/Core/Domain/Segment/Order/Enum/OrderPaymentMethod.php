<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Enum;

use App\Shared\Traits\Enum\HasEnumValue;

enum OrderPaymentMethod: string
{
    use HasEnumValue;

    case CARD = 'card';
    case CASH = 'cash';

    /**
     * @return string
    */
    public function getLabel(): string
    {
        return match ($this) {
            self::CARD => 'Credit/Debit Card',
            self::CASH => 'Cash on Delivery',
        };
    }

    /**
     * @return string[]
    */
    public static function toArray(): array
    {
        return [
            self::CARD->value => self::CARD->getLabel(),
            self::CASH->value => self::CASH->getLabel(),
        ];
    }
}
