<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Utils;

final class PathChecker
{
    /**
     * @param string $path
     *
     * @return bool
    */
    public static function isDiscountPath(string $path): bool
    {
        return str_starts_with($path, '/discounts');
    }
}
