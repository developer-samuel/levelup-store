<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Resource;

use App\Core\Domain\{
    Segment\Review\Entity\ReviewDetail,
    Segment\Review\ValueObject\ReviewListObject,
    Segment\Review\ValueObject\ReviewObject
};

/**
 * @phpstan-type ResourceArray array{
 *     reviewExists: bool,
 *     averageRating: float,
 *     totalRatings: int,
 *     totalFeedbacks: int,
 *     totalCount: int,
 *     ratingsCount: array<string, int>,
 *     lastReviewDetails: ReviewDetail[],
 *     lastReview: ReviewObject|null
 * }
*/
final class ReviewListResource
{
    /**
     * @param ReviewListObject|null $list
     *
     * @return ResourceArray|null
    */
    public static function toArray(?ReviewListObject $list): ?array
    {
        if ($list === null) {
            return null;
        }

        return [
            'reviewExists'      => $list->reviewExists,
            'averageRating'     => $list->averageRating,
            'totalRatings'      => $list->totalRatings,
            'totalFeedbacks'    => $list->totalFeedbacks,
            'totalCount'        => $list->totalCount,
            'ratingsCount'      => $list->ratingsCount,
            'lastReviewDetails' => $list->lastReviewDetails,
            'lastReview'        => $list->lastReview,
        ];
    }
}
