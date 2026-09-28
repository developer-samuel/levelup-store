<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Product\Entity\Variant\ProductVariant
};

/**
 * @property Collection<int, ProductVariant> $variants
 * @property Brand $brand
 * @property string $catalogCode
*/
trait ProductCoreTrait
{
    /**
     * @return Collection<int, ProductVariant>
    */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    /**
     * @return Brand
    */
    public function getBrand(): Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     *
     * @return self
    */
    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return string
    */
    public function getCatalogCode(): string
    {
        return $this->catalogCode;
    }

    /**
     * @param string $catalogCode
     *
     * @return self
    */
    public function setCatalogCode(string $catalogCode): self
    {
        $this->catalogCode = $catalogCode;
        return $this;
    }
}
