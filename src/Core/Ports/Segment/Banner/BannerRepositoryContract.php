<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Banner;

use App\Core\Domain\Segment\Banner\Entity\Banner;

interface BannerRepositoryContract
{
    /**
     * @return Banner[]
    */
    public function findAll(): array;

    /**
     * @return Banner[]
    */
    public function findAllActive(): array;

    /**
     * @return int
    */
    public function findMaxPosition(): int;
}
