<?php

declare(strict_types=1);

namespace Database\Seeds\Factories;

use App\Core\Domain\Segment\Brand\Brand;

trait BrandFactory
{
    /**
     * @param string $name
     *
     * @return Brand
    */
    private function createBrand(string $name): Brand
    {
        return (new Brand())
            ->setName($name);
    }
}
