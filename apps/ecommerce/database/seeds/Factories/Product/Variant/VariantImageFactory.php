<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product\Variant;

use Doctrine\Persistence\ObjectManager;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantImage
};

trait VariantImageFactory
{
    /**
     * @param ObjectManager $manager
     * @param ProductVariant $variant
     * @param string[] $imagePaths
     *
     * @return void
    */
    private function createProductVariantImages(
        ObjectManager $manager,
        ProductVariant $variant,
        array $imagePaths,
    ): void {
        $position = 1;

        foreach ($imagePaths as $imagePath) {
            $image = (new ProductVariantImage())
                ->setPosition($position++)
                ->setVariant($variant)
                ->setPath($imagePath);

            $manager->persist($image);
        }
    }
}
