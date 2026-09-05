<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use Kit\Utils\Shared\StringNormalizer;

use App\Core\Domain\{
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\Catalog\ProductCatalogObject,
    Segment\Product\ValueObject\Catalog\ProductCatalogFilterObject,
    Segment\Product\ValueObject\Catalog\ProductCatalogPaginationObject,
    Segment\Product\ValueObject\ProductFilterObject,
    Segment\Product\ValueObject\ProductPaginationObject
};

use App\Core\Application\{
    Segment\Product\Resource\ProductCatalogResource,
    Segment\Product\Resource\ProductSortResource
};

use App\Core\Ports\{
    Segment\Brand\BrandRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Product\Service\Query\ProductCategoryQueryContract,
    Segment\Product\Service\Query\ProductQueryContract,
    Segment\Product\Service\Query\ProductVariantQueryContract
};

final readonly class ProductQueryService implements ProductQueryContract
{
    private const LIMIT = 12;

    /**
     * @param BrandRepositoryContract $brandRepository
     * @param ProductVariantRepositoryContract $variantRepository
     * @param ProductVariantQueryContract $productVariantQuery
     * @param ProductCategoryQueryContract $productCategoryQuery
    */
    public function __construct(
        private BrandRepositoryContract $brandRepository,
        private ProductVariantRepositoryContract $variantRepository,
        private ProductVariantQueryContract $productVariantQuery,
        private ProductCategoryQueryContract $productCategoryQuery,
    ) {}

    /**
     * @param ProductFilterObject $filter
     * @param int $currentPage
     * @param ProductSortOption $sort
     *
     * @return array<string, mixed>
    */
    public function getFilteredAndSortedData(
        ProductFilterObject $filter,
        int $currentPage = 1,
        ProductSortOption $sort = ProductSortOption::TOP_RATED,
    ): array {
        $limit = self::LIMIT;

        $isDiscountRoute = $filter->isDiscountRoute;

        [$category, $type] = $this->normalizeCategoryAndType($filter);

        $variantsData = $this->variantRepository->findAvailableVariantsPaginated(
            $filter,
            $currentPage,
            $limit,
            $sort,
        );

        $items = $variantsData['items'];
        $totalCount = $variantsData['total'];

        $paginatedData = $this->productVariantQuery->mapVariantsToData($items);

        $typesAndSubtypes = $this->productCategoryQuery->getTypesAndSubtypes($category, $type);

        $pagination = $this->buildPagination($currentPage, $limit);

        $data = [
            'isDiscountRoute' => $isDiscountRoute,
            'category'        => $category,
            'type'            => $type,
            'filtered'        => $paginatedData,
            'types'           => $typesAndSubtypes['types'],
            'subtypes'        => $typesAndSubtypes['subtypes'],
            'pagination'      => $pagination,
            'totalCount'      => $totalCount,
        ];

        return $this->formatData($filter, $data, $sort);
    }

    /**
     * @param ProductFilterObject $filter
     *
     * @return array{
     *     0: string|null,
     *     1: string|null
     * }
    */
    private function normalizeCategoryAndType(ProductFilterObject $filter): array
    {
        return [
            $this->normalizeSlug($filter->category),
            $this->normalizeSlug($filter->type),
        ];
    }

    /**
     * @param string|null $value
     *
     * @return string|null
    */
    private function normalizeSlug(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(
            StringNormalizer::toLowerCase($value),
        );

        return $value === '' ? null : $value;
    }

    /**
     * @param int $currentPage
     * @param int $limit
     *
     * @return ProductPaginationObject
    */
    private function buildPagination(int $currentPage, int $limit): ProductPaginationObject
    {
        $offset = ($currentPage - 1) * $limit;

        return new ProductPaginationObject(
            currentPage: $currentPage,
            limit: $limit,
            offset: $offset,
        );
    }

    /**
     * @param ProductFilterObject $filter
     * @param array{
     *     isDiscountRoute: bool,
     *     category: string|null,
     *     type: string|null,
     *     types: string[],
     *     subtypes: string[],
     *     filtered: array<string, mixed>,
     *     pagination: ProductPaginationObject
     * } $data
     * @param ProductSortOption $sort
     *
     * @return array<string, mixed>
    */
    private function formatData(ProductFilterObject $filter, array $data, ProductSortOption $sort): array
    {
        $catalog = new ProductCatalogObject(
            isDiscountRoute: $data['isDiscountRoute'],
            filter: $this->createFilter($filter, $data),
            pagination: $this->createPagination($data['pagination'], $data['totalCount'] ?? count($data['filtered'])),
            variants: $data['filtered'],
            sortOptions: $this->getSortOptions(),
            sort: $sort->value,
        );

        return ProductCatalogResource::toArray($catalog);
    }

    /**
     * @param ProductPaginationObject $pagination
     * @param int $totalCount
     *
     * @return ProductCatalogPaginationObject
    */
    private function createPagination(ProductPaginationObject $pagination, int $totalCount): ProductCatalogPaginationObject
    {
        $limit = self::LIMIT;
        $maxPages = (int) ceil($totalCount / $limit);
        $showLoadMore = $totalCount > ($pagination->offset + $limit);

        return new ProductCatalogPaginationObject(
            pagination:   $pagination,
            currentPage:  $pagination->currentPage,
            maxPages:     $maxPages,
            totalCount:   $totalCount,
            showLoadMore: $showLoadMore,
        );
    }

    /**
     * @param ProductFilterObject $filter
     * @param array{
     *     types: string[],
     *     subtypes: string[],
     *     filtered: array<string, mixed>,
     *     category: string|null,
     *     type: string|null
     * } $data
     *
     * @return ProductCatalogFilterObject
    */
    private function createFilter(ProductFilterObject $filter, array $data): ProductCatalogFilterObject
    {
        return new ProductCatalogFilterObject(
            types: $data['types'],
            subtypes: $data['subtypes'],
            brands: $this->brandRepository->findAllWithProducts($data['category'], $data['type']),
            maxPrice: $this->variantRepository->getMaxPriceForFilter($filter),
            filtered: $data['filtered'],
            category: $data['category'],
            type: $data['type'],
        );
    }

    /**
     * @return array<int, string[]>
    */
    private function getSortOptions(): array
    {
        return array_map(
            fn(ProductSortOption $option): array => ProductSortResource::toArray($option),
            ProductSortOption::cases(),
        );
    }
}
