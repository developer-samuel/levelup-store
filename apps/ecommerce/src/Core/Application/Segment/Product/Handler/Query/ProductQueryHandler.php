<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Handler\Query;

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Domain\{
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject
};

use App\Core\Ports\{
    Segment\Product\Handler\Query\ProductQueryHandlerContract,
    Segment\Product\Service\Query\ProductQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

final readonly class ProductQueryHandler implements ProductQueryHandlerContract
{
    /**
     * @param ProductQueryContract $productQuery
     * @param ReviewQueryContract $reviewQuery
    */
    public function __construct(
        private ProductQueryContract $productQuery,
        private ReviewQueryContract $reviewQuery,
    ) {}

    /**
     * @param ProductFilterObject $filter
     * @param int $currentPage
     * @param ProductSortOption $sort
     *
     * @return array<string, mixed>
    */
    public function handle(
        ProductFilterObject $filter,
        int $currentPage = 1,
        ProductSortOption $sort = ProductSortOption::TOP_RATED,
    ): array {
        $data = $this->productQuery->getFilteredAndSortedData($filter, $currentPage, $sort);

        $data['products'] = $this->processProducts($data['products'] ?? []);

        $this->sanitizeData($data);

        return $data;
    }

    /**
     * @param mixed $products
     *
     * @return array<array<string, mixed>>
    */
    private function processProducts(mixed $products): array
    {
        if (!is_array($products)) {
            return [];
        }

        return array_map(
            fn($item) => $this->processSingleProduct($item),
            array_filter($products, 'is_array'),
        );
    }

    /**
     * @param array<string|int, mixed> $item
     *
     * @return array<string, mixed>
    */
    private function processSingleProduct(array $item): array
    {
        $item = $this->normalizeKeys($item);
        $item['averageRating'] = $this->getAverageRatingForItem($item);

        return $item;
    }

    /**
     * @param array<string|int, mixed> $item
     *
     * @return array<string, mixed>
    */
    private function normalizeKeys(array $item): array
    {
        return array_combine(
            array_map(static fn(string|int $key): string => (string) $key, array_keys($item)),
            array_values($item),
        );
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return float
    */
    private function getAverageRatingForItem(array $item): float
    {
        $variantId = DataSanitizer::sanitizeInt($item['variantId'] ?? null);

        return $variantId !== null
            ? $this->reviewQuery->getAverageRatingByVariant($variantId)
            : 0;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
    */
    private function sanitizeData(array &$data): void
    {
        $data['showLoadMore'] = DataSanitizer::sanitizeBoolean($data['showLoadMore'] ?? false);

        $pagination = $data['pagination'] ?? null;
        if (!is_array($pagination)) {
            return;
        }

        $pagination['maxPages'] = $pagination['maxPages'] ?? 0;
        $pagination['currentPage'] = $pagination['currentPage'] ?? 1;
        $pagination['totalCount'] = $pagination['totalCount'] ?? 0;

        $data['pagination'] = $pagination;
        $data['showLoadMore'] = $pagination['showLoadMore'];
    }
}
