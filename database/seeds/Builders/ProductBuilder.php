<?php

declare(strict_types=1);

namespace Database\Seeds\Builders;

use Doctrine\Persistence\ObjectManager;

use Kit\Assertion\Domain\Brand\BrandAssertion;

use Database\{
    Seeds\Factories\Product\ProductFactory,
    Seeds\Factories\Product\ProductSubtypeFactory,
    Seeds\Factories\Product\Variant\VariantFactory
};

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type
};

trait ProductBuilder
{
    use ProductFactory;
    use ProductSubtypeFactory;
    use VariantFactory;

    /**
     * @param ObjectManager $manager
     * @param string $productName
     * @param array{
     *     brand: string,
     *     variants: array<string, array{
     *         price: float,
     *         description: string,
     *         discountPrice: float|null,
     *         stocks_available: int,
     *         stocks_reserved: int,
     *         images: string[],
     *         descriptions: array<array{string, string}>
     *     }>,
     *     subtypes: string[]
     * } $productData
     * @param Category $category
     * @param Type $type
     *
     * @return void
    */
    private function createProductWithVariants(
        ObjectManager $manager,
        string $productName,
        array $productData,
        Category $category,
        Type $type,
    ): void {
        $brand = $this->resolveBrand($productData['brand']);

        $product = $this->createProduct($productName, $category, $type, $brand);

        $manager->persist($product);

        foreach ($productData['variants'] as $variantName => $variantData) {
            /** @var array{
             *     price: float,
             *     description: string,
             *     discountPrice: float|null,
             *     stocks_available: int,
             *     stocks_reserved: int,
             *     images: string[],
             *     descriptions: array<array{string, string}>
             * } $variantData */
            $this->createVariant($manager, $product, $variantName, $variantData);
        }

        /** @var string[] $subtypes */
        $subtypes = $productData['subtypes'];

        $this->createProductSubtypes($manager, $product, $subtypes);
    }

    /**
     * @param string $brandName
     *
     * @return Brand
    */
    private function resolveBrand(string $brandName): Brand
    {
        return BrandAssertion::assertExistsWithIdentifier(
            $this->brandRepository->findByName($brandName),
            $brandName,
        );
    }
}
