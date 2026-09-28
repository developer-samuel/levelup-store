<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Review;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Review\Entity\Review;

final readonly class ReviewAssertion
{
    /**
     * @param Review|null $review
     *
     * @return void
     *
     * @phpstan-assert Review $review
    */
    public static function assertExists(?Review $review): void
    {
        ExistenceAssertion::assertExists($review, 'Review');
    }
}
