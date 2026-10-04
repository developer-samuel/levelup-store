<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product\Variant;

use Doctrine\Persistence\ObjectManager;

use Database\Seeds\Utils\Generator\EanCodeGenerator;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

trait VariantEanFactory
{
    private function createProductVariantEans(
        ObjectManager $manager,
        ProductVariant $variant,
        int $quantityAvailable,
        int $quantityReserved,
    ): void {
        $this->createActiveEans($manager, $variant, $quantityAvailable);
        $this->createReservedEans($manager, $variant, $quantityReserved);
    }

    private function createActiveEans(ObjectManager $manager, ProductVariant $variant, int $quantity): void
    {
        for ($i = 0; $i < $quantity; $i++) {
            $manager->persist(
                (new ProductVariantEan())
                    ->setVariant($variant)
                    ->setCode(EanCodeGenerator::generateEan13())
                    ->setStatus(ProductVariantEanStatus::ACTIVE),
            );
        }
    }

    private function createReservedEans(ObjectManager $manager, ProductVariant $variant, int $quantity): void
    {
        for ($i = 0; $i < $quantity; $i++) {
            $manager->persist(
                (new ProductVariantEan())
                    ->setVariant($variant)
                    ->setCode(EanCodeGenerator::generateEan13())
                    ->setStatus(ProductVariantEanStatus::RESERVED),
            );
        }
    }
}
