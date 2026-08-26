<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

final class ProductVariantProjection
{
    public const NAME = 'product_variants';

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
                    'name'            => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                    'effective_price' => ['type' => 'float'],
                    'has_discount'    => ['type' => 'boolean'],
                    'is_available'    => ['type' => 'boolean'],
                    'brand'           => ['type' => 'keyword'],
                    'category'        => ['type' => 'keyword'],
                    'type'            => ['type' => 'keyword'],
                    'subtypes'        => ['type' => 'keyword'],
                    'avg_rating'      => ['type' => 'float'],
                    'created_at'      => ['type' => 'date'],
                ],
            ],
        ];
    }
}
