<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Shared\Exception\ConflictException
};

use App\Core\Ports\{
    Segment\Order\Repository\OrderItemRepositoryContract,
    Segment\Review\Repository\ReviewRepositoryContract,
    Segment\Review\Service\Query\ReviewValidatorQueryContract
};

final readonly class ReviewValidatorQueryService implements ReviewValidatorQueryContract
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
     * @param int $value
     *
     * @return void
    */
    public function validate(User $user, int $variantId, int $value): void
    {
        $this->validateValue($value);
        $this->validatePurchase($user, $variantId);
        $this->validateAlreadyReviewed($user, $variantId);
    }

    /**
     * @param int $value
     *
     * @return void
     *
     * @throws \DomainException
    */
    private function validateValue(int $value): void
    {
        if ($value < 1 || $value > 5) {
            throw new \DomainException('Review value must be between 1 and 5.');
        }
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return void
     *
     * @throws AccessDeniedException
    */
    private function validatePurchase(User $user, int $variantId): void
    {
        if (!$this->orderItemRepository->hasPurchasedVariant($user, $variantId)) {
            throw new AccessDeniedException(
                'You cannot review a variant you have not purchased.',
            );
        }
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return void
     *
     * @throws ConflictException
    */
    private function validateAlreadyReviewed(User $user, int $variantId): void
    {
        if ($this->reviewRepository->existsByVariantAndUser($variantId, $user)) {
            throw new ConflictException(
                'You have already reviewed this variant.',
            );
        }
    }
}
