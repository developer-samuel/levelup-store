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
    /**
     * @return ProductVariant[]
    */
    public function findAll(): array;

    /**
     * @param ProductFilterObject $filter
     * @param int $page
     * @param int $limit
     * @param ProductSortOption|null $sort
     *
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

    /**
     * @param Product $product
     *
     * @return ProductVariant[]
    */
    public function findAllByProduct(Product $product): array;

    /**
     * @param ProductFilterObject $filter
     *
     * @return float
    */
    public function getMaxPriceForFilter(ProductFilterObject $filter): float;

    /**
     * @param string $searchTerm
     *
     * @return ProductVariant[]
    */
    public function searchByName(string $searchTerm): array;

    /**
     * @param string $url
     *
     * @return ProductVariant|null
    */
    public function findOneByUrl(string $url): ?ProductVariant;

    /**
     * @param int $id
     *
     * @return ProductVariant|null
    */
    public function findById(int $id): ?ProductVariant;

    /**
     * @param int[] $excludedVariantIds
     *
     * @return ProductVariant|null
    */
    public function findRandomAvailableExcluding(array $excludedVariantIds): ?ProductVariant;
}
