<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Brand\Resource;

use App\Core\Domain\Segment\Brand\Brand;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     name: string,
 *     createdAt: string
 * }
*/
final class AdminApiBrandResource
{
    /**
     * @param Brand $brand
     *
     * @return ResourceArray
    */
    public static function toArray(Brand $brand): array
    {
        return [
            'id'        => $brand->getId(),
            'name'      => $brand->getName(),
            'createdAt' => DateTimeFormatter::formatDMY($brand->getCreatedAt()),
        ];
    }
}
