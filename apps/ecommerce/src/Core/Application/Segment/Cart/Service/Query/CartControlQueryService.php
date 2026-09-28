<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Query;

use Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartControlQueryContract
};

final readonly class CartControlQueryService implements CartControlQueryContract
{
    /**
     * @param CartRepositoryContract $cartRepository
    */
    public function __construct(
        private CartRepositoryContract $cartRepository,
    ) {}

    /**
     * @param User $user
     *
     * @return Cart|null
    */
    public function getUserCart(User $user): ?Cart
    {
        $userId = IdAssertion::assert(
            $user->getId(),
            'User ID',
        );

        return $this->cartRepository->findCartForUser($userId);
    }
}
