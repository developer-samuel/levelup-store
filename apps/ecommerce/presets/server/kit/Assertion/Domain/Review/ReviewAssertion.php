<?php

declare(strict_types=1);

namespace Presets\Kit\Assertion\Domain\Review;

use Presets\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Review\Entity\Review;

final readonly class ReviewAssertion
{
    /** @phpstan-assert Review $review */
    public static function assertExists(?Review $review): void
    {
        ExistenceAssertion::assertExists($review, 'Review');
    }
}
