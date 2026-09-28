<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Service\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\Entity\Review,
    Segment\Review\ValueObject\ReviewListObject,
    Segment\User\Entity\User
};

interface ReviewQueryContract
{
    /**
     * @param int $variantId
     *
     * @return float
    */
    public function getAverageRatingByVariant(int $variantId): float;

    /**
     * @param list<int> $variantIds
     *
     * @return array<int, float> [variantId => average]
    */
    public function getAverageRatingsForVariants(array $variantIds): array;

    /**
     * @param ProductVariant $variant
     * @param User|null $user
     *
     * @return ReviewListObject
    */
    public function getLastReviewData(ProductVariant $variant, ?User $user): ReviewListObject;

    /**
     * @param Review $review
     *
     * @return bool
    */
    public function hasDetails(Review $review): bool;

    /**
     * @param string[] $details
     *
     * @return string[]
    */
    public function limitDetails(array $details): array;
}
