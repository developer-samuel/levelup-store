<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product\Variant;

use Doctrine\Persistence\ObjectManager;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDiscount
};

trait VariantDiscountFactory
{
    /**
     * @param ObjectManager $manager
     * @param ProductVariant $variant
     * @param float $discountPrice
     *
     * @return void
     */
    private function applyDiscountToVariant(
        ObjectManager $manager,
        ProductVariant $variant,
        float $discountPrice,
    ): void {
        $discount = (new ProductVariantDiscount())
            ->setVariant($variant)
            ->setPrice($discountPrice);

        $manager->persist($discount);
    }
}
