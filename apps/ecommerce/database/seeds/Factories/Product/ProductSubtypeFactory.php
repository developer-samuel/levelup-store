<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product;

use Doctrine\Persistence\ObjectManager;

use Kit\Assertion\Domain\Subtype\SubtypeAssertion;

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Entity\ProductSubtype,
    Segment\Subtype\Entity\Subtype
};

trait ProductSubtypeFactory
{
    /**
     * @param ObjectManager $manager
     * @param Product $product
     * @param string[] $subtypes
     *
     * @return void
     *
     * @throws \LogicException
    */
    private function createProductSubtypes(
        ObjectManager $manager,
        Product $product,
        array $subtypes,
    ): void {
        foreach ($subtypes as $subtypeName) {
            $subtype = $manager->getRepository(Subtype::class)->findOneBy([
                'name'     => $subtypeName,
                'type'     => $product->getType(),
                'category' => $product->getCategory(),
            ]);
            SubtypeAssertion::assertExists($subtype);

            $productSubtype = (new ProductSubtype())
                ->setProduct($product)
                ->setSubtype($subtype);

            $manager->persist($productSubtype);
        }
    }
}
