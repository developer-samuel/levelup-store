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

/**
 * @property Collection<int, ProductVariantDescription> $descriptions
 * @property Collection<int, ProductVariantEan> $eans
 * @property Collection<int, ProductVariantImage> $images
 * @property string|null $description
 * @property string|null $sku
 * @property ProductVariantDiscount|null $discount
 * @property float $price
 * @property ProductVariantStatus $status
 * @property ProductVariantStock|null $stock
*/
trait ProductVariantCoreTrait
{
    /**
     * @return Collection<int, ProductVariantDescription>
    */
    public function getDescriptions(): Collection
    {
        return $this->descriptions;
    }

    /**
     * @return Collection<int, ProductVariantEan>
    */
    public function getEans(): Collection
    {
        return $this->eans;
    }

    /**
     * @param Collection<int, ProductVariantEan> $eans
     *
     * @return self
    */
    public function setEans(Collection $eans): self
    {
        $this->eans = $eans;
        return $this;
    }

    /**
     * @return Collection<int, ProductVariantImage>
    */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param Collection<int, ProductVariantImage> $images
     *
     * @return self
    */
    public function setImages(Collection $images): self
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return string|null
    */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     *
     * @return self
    */
    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return string|null
    */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return self
    */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return ProductVariantImage|null
    */
    public function getImage(): ?ProductVariantImage
    {
        $image = $this->images->first();

        return $image !== false ? $image : null;
    }

    /**
     * @return ProductVariantDiscount|null
    */
    public function getDiscount(): ?ProductVariantDiscount
    {
        return $this->discount;
    }

    /**
     * @param ProductVariantDiscount|null $discount
     *
     * @return self
    */
    public function setDiscount(?ProductVariantDiscount $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return float
    */
    public function getDiscountedPrice(): float
    {
        return $this->discount !== null
            ? ($this->price - $this->discount->getPrice())
            : $this->price;
    }

    /**
     * @return ProductVariantStatus
    */
    public function getStatus(): ProductVariantStatus
    {
        return $this->status;
    }

    /**
     * @param ProductVariantStatus $status
     *
     * @return self
    */
    public function setStatus(ProductVariantStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return ProductVariantStock|null
    */
    public function getStock(): ?ProductVariantStock
    {
        return $this->stock;
    }

    /**
     * @param ProductVariantStock|null $stock
    */
    public function setStock(?ProductVariantStock $stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @return ProductVariantStock|null
    */
    public function getInStock(): ?ProductVariantStock
    {
        if ($this->stock !== null && $this->stock->getStatus() === ProductStockStatus::IN_STOCK) {
            return $this->stock;
        }

        return null;
    }
}
