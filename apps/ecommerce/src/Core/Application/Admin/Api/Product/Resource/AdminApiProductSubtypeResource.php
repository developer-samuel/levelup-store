<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Resource;

use App\Core\Domain\Segment\Product\Entity\ProductSubtype;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     name: string,
 *     createdAt: string
 * }
*/
final class AdminApiProductSubtypeResource
{
    /**
     * @param ProductSubtype $subtype
     *
     * @return ResourceArray
    */
    public static function toArray(ProductSubtype $subtype): array
    {
        return [
            'id'        => $subtype->getId(),
            'name'      => $subtype->getSubtype()->getName(),
            'createdAt' => DateTimeFormatter::formatDMY($subtype->getCreatedAt()),
        ];
    }
}
