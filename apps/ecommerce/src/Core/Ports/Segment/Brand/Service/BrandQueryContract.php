<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Brand\Service;

use App\Core\Domain\Segment\Brand\Brand;

interface BrandQueryContract
{
    /**
     * @param int $id
     *
     * @return Brand
    */
    public function getBrandByIdOrFail(int $id): Brand;
}
