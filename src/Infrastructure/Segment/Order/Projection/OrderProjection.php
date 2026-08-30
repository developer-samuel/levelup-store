<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Projection;

use App\Infrastructure\Abstract\Projection\AbstractProjection;

class OrderProjection extends AbstractProjection
{
    public const NAME = 'orders';

    /**
     * @return array<string, mixed>
    */
    protected static function properties(): array
    {
        return [
            'code'           => self::KEYWORD,
            'status'         => self::KEYWORD,
            'payment_method' => self::KEYWORD,
            'price'          => self::FLOAT,
            'has_payment'    => self::BOOLEAN,
            'send_shipping'  => self::BOOLEAN,
            'user_id'        => self::INTEGER,
            'created_at'     => self::DATE,
        ];
    }
}
