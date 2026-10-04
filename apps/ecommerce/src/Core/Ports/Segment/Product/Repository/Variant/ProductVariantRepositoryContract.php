<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject
};

interface ProductVariantRepositoryContract
{
    /** @return ProductVariant[] */
    public function findAll(): array;

    /**
     * @return array{
     *     items: ProductVariant[],
     *     total: int
     * }
    */
    public function findAvailableVariantsPaginated(
        ProductFilterObject $filter,
        int $page,
        int $limit,
        ?ProductSortOption $sort = null,
    ): array;

    /** @return ProductVariant[] */
    public function findAllByProduct(Product $product): array;

    public function getMaxPriceForFilter(ProductFilterObject $filter): float;

    /** @return ProductVariant[] */
    public function searchByName(string $searchTerm): array;

    public function findOneByUrl(string $url): ?ProductVariant;
    public function findById(int $id): ?ProductVariant;

    /** @param int[] $excludedVariantIds */
    public function findRandomAvailableExcluding(array $excludedVariantIds): ?ProductVariant;
}
