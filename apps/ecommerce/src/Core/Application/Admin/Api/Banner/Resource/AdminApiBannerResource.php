<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Banner\Resource;

use App\Core\Domain\Segment\Banner\Entity\Banner;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     name: string,
 *     isActive: bool,
 *     createdAt: string
 * }
*/
final class AdminApiBannerResource
{
    /**
     * @param Banner $banner
     *
     * @return ResourceArray
    */
    public static function toArray(Banner $banner): array
    {
        return [
            'id'        => $banner->getId(),
            'name'      => $banner->getName(),
            'isActive'  => $banner->getIsActive(),
            'createdAt' => DateTimeFormatter::formatDMY($banner->getCreatedAt()),
        ];
    }
}
