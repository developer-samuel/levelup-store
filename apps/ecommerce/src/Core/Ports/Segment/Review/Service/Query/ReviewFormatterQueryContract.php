<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Service\Query;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\User\Entity\User
};

/**
 * @phpstan-type ReviewWithRatings array{
 *     review: Review,
 *     likesCount: int,
 *     dislikesCount: int,
 *     userRatingType: string|null
 * }
 * @phpstan-type ReviewData array{
 *     average: float,
 *     totalRatings: int,
 *     totalFeedbacks: int,
 *     ratingsCount: array<string, int>
 * }
*/
interface ReviewFormatterQueryContract
{
    /** @return array<int, ReviewWithRatings> */
    public function getFormattedReviewsForVariant(int $variantId, ?User $user): array;

    /** @return ReviewData */
    public function getFormattedReviewData(int $variantId): array;
}
