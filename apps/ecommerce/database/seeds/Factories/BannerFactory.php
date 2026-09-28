<?php

declare(strict_types=1);

namespace Database\Seeds\Factories;

use App\Core\Domain\{
    Segment\Banner\Entity\Banner,
    Segment\Banner\Enum\BannerType
};

trait BannerFactory
{
    /**
     * @param int $position
     * @param string $name
     * @param string|null $image
     * @param string|null $url
     * @param BannerType $type
     * @param bool $isActive
     *
     * @return Banner
    */
    private function createBanner(
        int $position,
        string $name,
        ?string $image,
        ?string $url,
        BannerType $type,
        bool $isActive,
    ): Banner {
        return (new Banner())
            ->setPosition($position)
            ->setName($name)
            ->setImage($image)
            ->setUrl($url)
            ->setType($type)
            ->setIsActive($isActive);
    }
}
