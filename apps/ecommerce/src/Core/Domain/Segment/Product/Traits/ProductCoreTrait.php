<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Product\Entity\Variant\ProductVariant
};

trait ProductCoreTrait
{
    /** @return Collection<int, ProductVariant> */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getCatalogCode(): string
    {
        return $this->catalogCode;
    }

    public function setCatalogCode(string $catalogCode): self
    {
        $this->catalogCode = $catalogCode;
        return $this;
    }
}
