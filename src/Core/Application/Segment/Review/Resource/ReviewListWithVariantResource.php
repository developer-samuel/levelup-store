<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Resource;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\Entity\ReviewDetail,
    Segment\Review\ValueObject\ReviewListObject,
    Segment\Review\ValueObject\ReviewObject
};

/**
 * @phpstan-import-type ResourceArray from ReviewListResource
 */
final class ReviewListWithVariantResource
{
    /**
     * @param ReviewListObject|null $list
     * @param ProductVariant $variant
     *
     * @return array{
     *     reviewExists: bool,
     *     averageRating: float,
     *     totalRatings: int,
     *     totalFeedbacks: int,
     *     totalCount: int,
     *     ratingsCount: array<string, int>,
     *     lastReviewDetails: ReviewDetail[],
     *     lastReview: ReviewObject|null,
     *     reviews: ReviewObject[],
     *     variant: ProductVariant
     * }|array{}
    */
    public static function toArray(?ReviewListObject $list, ProductVariant $variant): array
    {
        if ($list === null) {
            return [];
        }

        /** @var ResourceArray $base */
        $base = ReviewListResource::toArray($list);

        return [
            ...$base,
            'reviews' => array_values($list->reviews),
            'variant' => $variant,
        ];
    }
}
