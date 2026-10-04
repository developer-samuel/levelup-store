<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product\Variant;

use Doctrine\Persistence\ObjectManager;

use Packages\Kit\{
    Utils\Product\ProductCatalogCodeGenerator,
    Utils\Shared\IdentifierGenerator,
    Utils\Shared\StringNormalizer
};

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Enum\Variant\ProductVariantStatus
};

trait VariantFactory
{
    use VariantDescriptionFactory;
    use VariantDiscountFactory;
    use VariantEanFactory;
    use VariantImageFactory;
    use VariantStockFactory;

    /**
     * @param array{
     *   price: float,
     *   description: string,
     *   discountPrice: float|null,
     *   stocks_available: int,
     *   stocks_reserved: int,
     *   images: string[],
     *   descriptions: array<array{0:string,1:string}>
     * } $variantData
    */
    private function createVariant(
        ObjectManager $manager,
        Product $product,
        string $variantName,
        array $variantData,
    ): void {
        $variant = $this->buildVariant($product, $variantName, $variantData);

        if ($variantData['discountPrice'] !== null) {
            $this->applyDiscountToVariant($manager, $variant, (float) $variantData['discountPrice']);
        }

        $manager->persist($variant);

        $this->handleVariantStocks($manager, $variant, $variantData);
        $this->createProductVariantImages($manager, $variant, $variantData['images']);
        $this->createProductVariantDescriptions($manager, $variant, $variantData['descriptions']);
        $this->handleVariantEans($manager, $variant, $variantData);
    }

    /**
     * @param array{
     *   price: float|int,
     *   description: string|null,
     *   discountPrice: float|null,
     *   stocks_available: int,
     *   stocks_reserved: int,
     *   images: string[],
     *   descriptions: array<array{0:string,1:string}>
     * } $variantData
    */
    private function buildVariant(Product $product, string $variantName, array $variantData): ProductVariant
    {
        return (new ProductVariant())
            ->setProduct($product)
            ->setSku($this->generateSku($product->getName(), $variantName))
            ->setName($variantName)
            ->setPrice((float) $variantData['price'])
            ->setDescription($variantData['description'] ?? null)
            ->setUrl($this->generateSlug($variantName))
            ->setStatus(ProductVariantStatus::AVAILABLE);
    }

    /**
     * @param array{
     *   stocks_available?: int,
     *   stocks_reserved?: int
     * } $variantData
    */
    private function handleVariantStocks(ObjectManager $manager, ProductVariant $variant, array $variantData): void
    {
        [$stocksAvailable, $stocksReserved] = $this->getStocks($variantData);

        $this->createProductVariantStock(
            $manager,
            $variant,
            $stocksAvailable,
            $stocksReserved,
        );
    }

    /**
     * @param array{
     *   stocks_available?: int,
     *   stocks_reserved?: int
     * } $variantData
    */
    private function handleVariantEans(ObjectManager $manager, ProductVariant $variant, array $variantData): void
    {
        [$stocksAvailable, $stocksReserved] = $this->getStocks($variantData);

        $this->createProductVariantEans(
            $manager,
            $variant,
            $stocksAvailable,
            $stocksReserved,
        );
    }

    private function generateSku(string $productName, ?string $variantName = null, int $randomLength = 6): string
    {
        $catalogCode = ProductCatalogCodeGenerator::generateCatalogCode($productName);
        $prefix = substr(str_replace('-', '', $catalogCode), 0, 6);

        $variantPart = $variantName !== null
            ? '-' . strtoupper(substr(str_replace(' ', '', $variantName), 0, 5))
            : '';

        $uniqueSuffix = '-' . IdentifierGenerator::generateRandomAlphanumeric($randomLength);

        return $prefix . $variantPart . $uniqueSuffix;
    }

    private function generateSlug(string $name): string
    {
        $slug = StringNormalizer::toLowerCase($name);
        $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $slug);

        return trim($slug, '-');
    }

    /**
     * @param array{
     *   stocks_available?: int,
     *   stocks_reserved?: int
     * } $variantData
     *
     * @return int[] [stocksAvailable, stocksReserved]
    */
    private function getStocks(array $variantData): array
    {
        $stocksAvailable = $variantData['stocks_available'] ?? 0;
        $stocksReserved = $variantData['stocks_reserved'] ?? 0;

        return [(int) $stocksAvailable, (int) $stocksReserved];
    }
}
