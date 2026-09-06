<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant
};

use App\Core\Application\{
    Admin\Api\Product\Resource\Variant\AdminApiVariantResource,
    Shared\Utils\Mapper\ResourceMapper
};

use App\Core\Ports\{
    Segment\Product\Repository\ProductRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract
};

final readonly class AdminApiVariantListQueryHandler
{
    /**
     * @param ProductRepositoryContract $productRepository
     * @param ProductVariantRepositoryContract $variantRepository
    */
    public function __construct(
        private ProductRepositoryContract $productRepository,
        private ProductVariantRepositoryContract $variantRepository,
    ) {}

    /**
     * @param int $productId
     *
     * @return list<array<string, mixed>>
     */
    public function handle(int $productId): array
    {
        $product = $this->getProduct($productId);
        if (!$product) {
            return [];
        }

        $variants = $this->getVariants($product);

        return ResourceMapper::collection(
            $variants,
            AdminApiVariantResource::class,
        );
    }

    /**
     * @param int $productId
     *
     * @return Product|null
    */
    private function getProduct(int $productId): ?Product
    {
        $product = $this->productRepository->findById($productId);

        return $product instanceof Product ? $product : null;
    }

    /**
     * @param Product $product
     *
     * @return array<int, ProductVariant>
    */
    private function getVariants(Product $product): array
    {
        return array_values($this->variantRepository->findAllByProduct($product));
    }
}
