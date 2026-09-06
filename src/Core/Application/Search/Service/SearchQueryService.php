<?php

declare(strict_types=1);

namespace App\Core\Application\Search\Service;

use App\Core\Domain\{
    Search\SearchResultObject,
    Segment\Product\Entity\Variant\ProductVariant
};

use App\Core\Application\Search\Factory\SearchResultFactory;

use App\Core\Ports\{
    Search\Service\SearchQueryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

final readonly class SearchQueryService implements SearchQueryContract
{
    /**
     * @param ProductVariantRepositoryContract $variantRepository
     * @param ReviewQueryContract $reviewQuery
     * @param SearchResultFactory $factory
    */
    public function __construct(
        private ProductVariantRepositoryContract $variantRepository,
        private ReviewQueryContract $reviewQuery,
        private SearchResultFactory $factory,
    ) {}

    /**
     * @param string $query
     *
     * @return array<int, array<string, mixed>>
    */
    public function searchByTerm(string $query): array
    {
        $variants = $this->variantRepository->searchByName($query);
        $variantIds = $this->extractVariantIds($variants);
        $ratings = $this->reviewQuery->getAverageRatingsForVariants($variantIds);

        return $this->mapVariantsToResults($variants, $ratings);
    }

    /**
     * @param ProductVariant[] $variants
     *
     * @return list<int>
    */
    private function extractVariantIds(array $variants): array
    {
        $ids = [];
        foreach ($variants as $variant) {
            $id = $variant->getId();
            if ($id !== null) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * @param ProductVariant[] $variants
     * @param array<int, float> $ratings
     *
     * @return array<int, array<string, mixed>>
     */
    private function mapVariantsToResults(array $variants, array $ratings): array
    {
        $results = [];
        foreach ($variants as $variant) {
            $results[] = (array) $this->transformResult($variant, $ratings);
        }

        return $results;
    }

    /**
     * @param ProductVariant $variant
     * @param array<int, float> $ratings
     *
     * @return SearchResultObject
    */
    private function transformResult(ProductVariant $variant, array $ratings): SearchResultObject
    {
        $averageRating = (float) ($ratings[$variant->getId()] ?? 0.0);

        return $this->factory->create($variant, $averageRating);
    }
}
