<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantRecommended;

use App\Core\Application\Segment\Product\Resource\ProductRecommendedResource;

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRecommendedRepositoryContract,
    Segment\Product\Service\Query\ProductRecommendedQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

/**
 * @phpstan-import-type ResourceArray from ProductRecommendedResource
*/
final readonly class ProductRecommendedQueryService implements ProductRecommendedQueryContract
{
    /**
     * @param ProductVariantRecommendedRepositoryContract $productRecommendedRepository
     * @param ReviewQueryContract $reviewQuery
    */
    public function __construct(
        private ProductVariantRecommendedRepositoryContract $productRecommendedRepository,
        private ReviewQueryContract $reviewQuery,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
    */
    public function findAll(): array
    {
        $variants = array_values(
            $this->getAllRecommendedVariants(),
        );

        return $this->convertVariantsToViewData($variants);
    }

    /**
     * @return array<string|int, ProductVariantRecommended>
    */
    private function getAllRecommendedVariants(): array
    {
        return $this->productRecommendedRepository->findAll();
    }

    /**
     * @param array<int, ProductVariantRecommended> $variants
     *
     * @return array<int, array<string, mixed>>
    */
    private function convertVariantsToViewData(array $variants): array
    {
        $normalizedVariants = array_values($variants);

        $variantIds = array_map(
            static fn(ProductVariantRecommended $v): int => $v->getVariant()->getId(),
            $normalizedVariants,
        );

        $ratings = $this->fetchRatings($variantIds);

        return $this->mapVariantsToViewData($normalizedVariants, $ratings);
    }

    /**
     * @param list<int> $variantIds
     *
     * @return array<int, float>
    */
    private function fetchRatings(array $variantIds): array
    {
        return $this->reviewQuery->getAverageRatingsForVariants($variantIds);
    }

    /**
     * @param array<int, ProductVariantRecommended> $variants
     * @param array<int, float> $ratings
     *
     * @return array<int, array<string, mixed>>
    */
    private function mapVariantsToViewData(array $variants, array $ratings): array
    {
        return array_map(
            fn(ProductVariantRecommended $variant): array => $this->transformVariant($variant, $ratings),
            $variants,
        );
    }

    /**
     * @param ProductVariantRecommended $variant
     * @param array<int, float> $ratings
     *
     * @return array<string, mixed>
    */
    private function transformVariant(ProductVariantRecommended $variant, array $ratings): array
    {
        $variantId = $variant->getVariant()->getId();
        $averageRating = $ratings[$variantId] ?? 0;

        return ProductRecommendedResource::toArray($variant, $averageRating);
    }
}
