<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Projection;

use App\Infrastructure\Abstract\Projection\AbstractProjection;

class UserProjection extends AbstractProjection
{
    public const NAME = 'users';

    /**
     * @return array<string, mixed>
    */
    protected static function properties(): array
    {
        return [
            'email'      => self::TEXT_WITH_KEYWORD,
            'first_name' => self::TEXT,
            'last_name'  => self::TEXT,
            'role'       => self::KEYWORD,
            'created_at' => self::DATE,
        ];
    }
}
