<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Projection;

final class ReviewProjection
{
    public const NAME = 'reviews';

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
                    'body'       => ['type' => 'text'],
                    'value'      => ['type' => 'float'],
                    'type'       => ['type' => 'keyword'],
                    'user_id'    => ['type' => 'integer'],
                    'variant_id' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                ],
            ],
        ];
    }
}
