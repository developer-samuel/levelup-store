<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Resource;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Product\ValueObject\Catalog\ProductCatalogObject,
    Segment\Product\ValueObject\ProductVariantObject
};

/**
 * @phpstan-type ResourceArray array{
 *     isDiscountRoute: bool,
 *     filter: ProductFilterShape,
 *     variants: ProductVariantObject[],
 *     sortOptions: array<int, string[]>,
 *     pagination: ProductPaginationShape,
 *     sort: string|null
 * }
 * @phpstan-type ProductFilterShape array{
 *     category: string|null,
 *     type: string|null,
 *     types: string[],
 *     subtypes: string[],
 *     brands: Brand[],
 *     maxPrice: float,
 *     filtered: array<string, mixed>
 * }
 * @phpstan-type ProductPaginationShape array{
 *     pagination: object,
 *     currentPage: int,
 *     maxPages: int,
 *     totalCount: int,
 *     showLoadMore: bool
 * }
*/
final class ProductCatalogResource
{
    /**
     * @param ProductCatalogObject $catalog
     *
     * @return ResourceArray
     */
    public static function toArray(ProductCatalogObject $catalog): array
    {
        /** @var ProductVariantObject[] $variants */
        $variants = $catalog->variants;

        return [
            'isDiscountRoute' => $catalog->isDiscountRoute,
            'filter'          => self::filterData($catalog),
            'variants'        => $variants,
            'sortOptions'     => $catalog->sortOptions,
            'pagination'      => self::paginationData($catalog),
            'sort'            => $catalog->sort,
        ];
    }

    /**
     * @param ProductCatalogObject $catalog
     *
     * @return ProductFilterShape
    */
    private static function filterData(ProductCatalogObject $catalog): array
    {
        $filter = $catalog->filter;

        return [
            'category' => $filter->category,
            'type'     => $filter->type,
            'types'    => $filter->types,
            'subtypes' => $filter->subtypes,
            'brands'   => $filter->brands,
            'maxPrice' => $filter->maxPrice,
            'filtered' => $filter->filtered,
        ];
    }

    /**
     * @param ProductCatalogObject $catalog
     *
     * @return ProductPaginationShape
    */
    private static function paginationData(ProductCatalogObject $catalog): array
    {
        $pagination = $catalog->pagination;

        return [
            'pagination'   => $pagination->pagination,
            'currentPage'  => $pagination->currentPage,
            'maxPages'     => $pagination->maxPages,
            'totalCount'   => $pagination->totalCount,
            'showLoadMore' => $pagination->showLoadMore,
        ];
    }
}
