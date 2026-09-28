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
    /**
     * @param OrderItemRepositoryContract $orderItemRepository
     * @param ReviewRepositoryContract $reviewRepository
    */
    public function __construct(
        private OrderItemRepositoryContract $orderItemRepository,
        private ReviewRepositoryContract $reviewRepository,
    ) {}

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function canUserCreateReview(User $user, int $variantId): bool
    {
        return $this->hasPurchasedVariant($user, $variantId)
            && $this->hasNotReviewedVariant($user, $variantId);
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    private function hasPurchasedVariant(User $user, int $variantId): bool
    {
        return $this->orderItemRepository->hasPurchasedVariant($user, $variantId);
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    private function hasNotReviewedVariant(User $user, int $variantId): bool
    {
        return !$this->reviewRepository->existsByVariantAndUser($variantId, $user);
    }
}
