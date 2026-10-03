<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits\Variant;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariantDescription,
    Segment\Product\Entity\Variant\ProductVariantDiscount,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Entity\Variant\ProductVariantImage,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\ProductStockStatus,
    Segment\Product\Enum\Variant\ProductVariantStatus
};

trait ProductVariantCoreTrait
{
    /** @return Collection<int, ProductVariantDescription> */
    public function getDescriptions(): Collection
    {
        return $this->descriptions;
    }

    /** @return Collection<int, ProductVariantEan> */
    public function getEans(): Collection
    {
        return $this->eans;
    }

    /** @param Collection<int, ProductVariantEan> $eans */
    public function setEans(Collection $eans): self
    {
        $this->eans = $eans;
        return $this;
    }

    /** @return Collection<int, ProductVariantImage> */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /** @param Collection<int, ProductVariantImage> $images */
    public function setImages(Collection $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?ProductVariantImage
    {
        $image = $this->images->first();

        return $image !== false ? $image : null;
    }

    public function getDiscount(): ?ProductVariantDiscount
    {
        return $this->discount;
    }

    public function setDiscount(?ProductVariantDiscount $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    public function getDiscountedPrice(): float
    {
        return $this->discount !== null
            ? ($this->price - $this->discount->getPrice())
            : $this->price;
    }

    public function getStatus(): ProductVariantStatus
    {
        return $this->status;
    }

    public function setStatus(ProductVariantStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStock(): ?ProductVariantStock
    {
        return $this->stock;
    }

    public function setStock(?ProductVariantStock $stock): void
    {
        $this->stock = $stock;
    }

    public function getInStock(): ?ProductVariantStock
    {
        if ($this->stock !== null && $this->stock->getStatus() === ProductStockStatus::IN_STOCK) {
            return $this->stock;
        }

        return null;
    }
}
