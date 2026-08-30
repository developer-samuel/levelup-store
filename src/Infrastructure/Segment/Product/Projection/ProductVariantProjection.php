<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

use App\Infrastructure\Abstract\Projection\AbstractProjection;

class ProductVariantProjection extends AbstractProjection
{
    public const NAME = 'product_variants';

    /**
     * @return array<string, mixed>
    */
    protected static function properties(): array
    {
        return [
            'name'            => self::TEXT_WITH_KEYWORD,
            'effective_price' => self::FLOAT,
            'has_discount'    => self::BOOLEAN,
            'is_available'    => self::BOOLEAN,
            'brand'           => self::KEYWORD,
            'category'        => self::KEYWORD,
            'type'            => self::KEYWORD,
            'subtypes'        => self::KEYWORD,
            'avg_rating'      => self::FLOAT,
            'created_at'      => self::DATE,
        ];
    }
}
