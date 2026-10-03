<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Segment\Order\Repository\OrderItemRepositoryContract,
    Segment\Review\Repository\ReviewRepositoryContract,
    Segment\Review\Service\Query\ReviewPermissionQueryContract
};

final readonly class ReviewPermissionQueryService implements ReviewPermissionQueryContract
{
    public function __construct(
        private OrderItemRepositoryContract $orderItemRepository,
        private ReviewRepositoryContract $reviewRepository,
    ) {}

    public function canUserCreateReview(User $user, int $variantId): bool
    {
        return $this->hasPurchasedVariant($user, $variantId)
            && $this->hasNotReviewedVariant($user, $variantId);
    }

    private function hasPurchasedVariant(User $user, int $variantId): bool
    {
        return $this->orderItemRepository->hasPurchasedVariant($user, $variantId);
    }
    
    private function hasNotReviewedVariant(User $user, int $variantId): bool
    {
        return !$this->reviewRepository->existsByVariantAndUser($variantId, $user);
    }
}
