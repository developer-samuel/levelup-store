<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Banner;

use App\Core\Domain\Segment\Banner\Entity\Banner;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     position: int,
 *     name: string,
 *     image: string|null,
 *     url: string|null,
 *     type: string,
 *     isActive: bool
 * }
*/
final class BannerResource
{
    /**
     * @param Banner $banner
     *
     * @return ResourceArray
    */
    public static function toArray(Banner $banner): array
    {
        return [
            'id'       => $banner->getId(),
            'position' => $banner->getPosition(),
            'name'     => $banner->getName(),
            'image'    => $banner->getImage(),
            'url'      => $banner->getUrl(),
            'type'     => $banner->getType()->value,
            'isActive' => $banner->getIsActive(),
        ];
    }
}
