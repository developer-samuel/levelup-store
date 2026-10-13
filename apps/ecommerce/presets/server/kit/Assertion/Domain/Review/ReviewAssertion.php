<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Review;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Review\Entity\Review;

final readonly class ReviewAssertion
{
    /** @phpstan-assert Review $review */
    public static function assertExists(?Review $review): void
    {
        ExistenceAssertion::assertExists($review, 'Review');
    }
}
