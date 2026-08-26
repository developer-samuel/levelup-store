<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Projection;

final class OrderProjection
{
    public const NAME = 'orders';

    /**
     * @return array<string, mixed>
    */
    public static function mapping(): array
    {
        return [
            'settings' => [
                'number_of_shards'   => 1,
                'number_of_replicas' => 0,
            ],
            'mappings' => [
                'properties' => [
                    'code'           => ['type' => 'keyword'],
                    'status'         => ['type' => 'keyword'],
                    'payment_method' => ['type' => 'keyword'],
                    'price'          => ['type' => 'float'],
                    'has_payment'    => ['type' => 'boolean'],
                    'send_shipping'  => ['type' => 'boolean'],
                    'user_id'        => ['type' => 'integer'],
                    'created_at'     => ['type' => 'date'],
                ],
            ],
        ];
    }
}
