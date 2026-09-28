<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product\Variant;

use Doctrine\Persistence\ObjectManager;

use Kit\Assertion\Domain\Product\Variant\ProductVariantStockAssertion;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\ProductStockStatus
};

trait VariantStockFactory
{
    /**
     * @param ObjectManager $manager
     * @param ProductVariant $variant
     * @param int $available
     * @param int $reserved
     *
     * @return void
    */
    private function createProductVariantStock(
        ObjectManager $manager,
        ProductVariant $variant,
        int $available,
        int $reserved,
    ): void {
        ProductVariantStockAssertion::assertStockQuantities($available, $reserved);

        $stock = (new ProductVariantStock())
            ->setVariant($variant)
            ->setQuantityAvailable($available)
            ->setQuantityReserved($reserved)
            ->setStatus(ProductStockStatus::IN_STOCK);

        $manager->persist($stock);
    }
}
