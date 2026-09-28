<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Review;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\Entity\Review,
    Segment\Review\Enum\ReviewType,
    Segment\User\Entity\User
};

trait ReviewFactory
{
    /**
     * @param ProductVariant $variant
     * @param User $user
     * @param float $value
     * @param string|null $body
     * @param ReviewType $type
     *
     * @return Review
     */
    private function createReview(
        ProductVariant $variant,
        User $user,
        float $value,
        ?string $body,
        ReviewType $type,
    ): Review {
        return (new Review())
            ->setVariant($variant)
            ->setUser($user)
            ->setValue($value)
            ->setBody($body)
            ->setType($type);
    }
}
