<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Handler\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\ValueObject\ReviewListWithVariantObject,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Review\Factory\ReviewListFactory;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Service\Query\ProductVariantQueryContract,
    Segment\Review\Handler\Query\ReviewListQueryHandlerContract,
    Segment\Review\Service\Query\ReviewFormatterQueryContract,
    Segment\Review\Service\Query\ReviewPermissionQueryContract
};

/**
 * @phpstan-import-type ReviewWithRatings from ReviewListFactory
 * @phpstan-import-type ReviewData from ReviewListFactory
*/
final readonly class ReviewListQueryHandler implements ReviewListQueryHandlerContract
{
    /**
     * @param SecurityProviderContract $securityProvider
     * @param ProductVariantQueryContract $productVariantQuery
     * @param ReviewFormatterQueryContract $reviewFormatterQuery
     * @param ReviewPermissionQueryContract $reviewPermissionQuery
     * @param ReviewListFactory $reviewListFactory
    */
    public function __construct(
        private SecurityProviderContract $securityProvider,
        private ProductVariantQueryContract $productVariantQuery,
        private ReviewFormatterQueryContract $reviewFormatterQuery,
        private ReviewPermissionQueryContract $reviewPermissionQuery,
        private ReviewListFactory $reviewListFactory,
    ) {}

    /**
     * @param string $url
     *
     * @return ReviewListWithVariantObject|null
    */
    public function handle(string $url): ?ReviewListWithVariantObject
    {
        $user = $this->securityProvider->getCurrentUser();

        $variant = $this->productVariantQuery->getVariantOrNull($url);
        if ($variant === null) {
            return null;
        }

        $variantId = $variant->getId();

        $reviewsWithRatings = $this->reviewFormatterQuery->getFormattedReviewsForVariant($variantId, $user);

        $reviewExists = $this->canCreateReview($user, $variantId);

        $reviewData = $this->reviewFormatterQuery->getFormattedReviewData($variantId);

        return $this->buildReviewList($reviewsWithRatings, $reviewExists, $reviewData, $variant);
    }

    /**
     * @param array<int, ReviewWithRatings> $reviewsWithRatings
     * @param bool $reviewExists
     * @param ReviewData $reviewData
     * @param ProductVariant $variant
     *
     * @return ReviewListWithVariantObject
    */
    private function buildReviewList(
        array $reviewsWithRatings,
        bool $reviewExists,
        array $reviewData,
        ProductVariant $variant,
    ): ReviewListWithVariantObject {
        return $this->reviewListFactory->fromObject(
            reviewsWithRatings: $reviewsWithRatings,
            reviewExists: $reviewExists,
            reviewData: $reviewData,
            variant: $variant,
        );
    }

    /**
     * @param User|null $user
     * @param int $variantId
     *
     * @return bool
    */
    private function canCreateReview(?User $user, int $variantId): bool
    {
        return $user !== null && $this->reviewPermissionQuery->canUserCreateReview($user, $variantId);
    }
}
