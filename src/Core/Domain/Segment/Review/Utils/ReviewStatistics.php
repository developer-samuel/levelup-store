<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Utils;

use App\Core\Domain\Segment\Review\Entity\Review;

final class ReviewStatistics
{
    /**
     * @param Review[] $reviews
     *
     * @return array{
     *   average: float,
     *   ratingsCount: int[],
     *   totalRatings: int,
     *   totalFeedbacks: int
     * }
    */
    public static function calculateSummary(array $reviews): array
    {
        $ratingsCount = self::initializeRatingsCount();
        $totalRatings = 0;
        $totalFeedbacks = 0;
        $allValuesForAverage = [];

        foreach ($reviews as $review) {
            $value = $review->getValue();
            $type = $review->getType()->value;

            self::incrementRatingsCount($ratingsCount, $value);
            self::incrementTotals($type, $value, $totalRatings, $totalFeedbacks, $allValuesForAverage);
        }

        return [
            'average'        => self::calculateAverage($allValuesForAverage),
            'ratingsCount'   => $ratingsCount,
            'totalRatings'   => $totalRatings,
            'totalFeedbacks' => $totalFeedbacks,
        ];
    }

    /**
     * @return int[]
    */
    private static function initializeRatingsCount(): array
    {
        return array_fill(1, 5, 0);
    }

    /**
     * @param int[] $ratingsCount
     * @param float $value
     *
     * @return void
    */
    private static function incrementRatingsCount(array &$ratingsCount, float $value): void
    {
        $intValue = (int) $value;

        if (isset($ratingsCount[$intValue])) {
            $ratingsCount[$intValue]++;
        }
    }

    /**
     * @param string $type
     * @param float $value
     * @param int &$totalRatings
     * @param int &$totalFeedbacks
     * @param float[] &$allValuesForAverage
     *
     * @return void
    */
    private static function incrementTotals(
        string $type,
        float $value,
        int &$totalRatings,
        int &$totalFeedbacks,
        array &$allValuesForAverage,
    ): void {
        if ($type === 'rating') {
            $totalRatings++;
            $allValuesForAverage[] = $value;
        } elseif ($type === 'feedback') {
            $totalFeedbacks++;
            $allValuesForAverage[] = $value;
        }
    }

    /**
     * @param float[] $values
     *
     * @return float
    */
    private static function calculateAverage(array $values): float
    {
        return !empty($values) ? round(array_sum($values) / count($values), 2) : 0.0;
    }
}
