<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product\Variant;

use Doctrine\Persistence\ObjectManager;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

trait VariantDescriptionFactory
{
    /**
     * @param ObjectManager $manager
     * @param ProductVariant $variant
     * @param array<array{0:string,1:string}> $descriptions
     *
     * @return void
    */
    private function createProductVariantDescriptions(
        ObjectManager $manager,
        ProductVariant $variant,
        array $descriptions,
    ): void {
        $position = 1;

        foreach ($descriptions as $descriptionData) {
            $title = $descriptionData[0];
            $body = $descriptionData[1];

            $description = (new ProductVariantDescription())
                ->setVariant($variant)
                ->setPosition($position++)
                ->setTitle($title)
                ->setBody($body);

            $manager->persist($description);
        }
    }
}
