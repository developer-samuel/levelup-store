<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Projection;

use App\Infrastructure\Abstract\Projection\AbstractProjection;

class ReviewProjection extends AbstractProjection
{
    public const NAME = 'reviews';

    /**
     * @return array<string, mixed>
    */
    protected static function properties(): array
    {
        return [
            'body'       => self::TEXT,
            'value'      => self::FLOAT,
            'type'       => self::KEYWORD,
            'user_id'    => self::INTEGER,
            'variant_id' => self::INTEGER,
            'created_at' => self::DATE,
        ];
    }
}
