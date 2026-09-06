<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Wishlist\Handler\Command;

use Kit\Assertion\Domain\User\UserAssertion;

use App\Core\Domain\Segment\Wishlist\Payload\WishlistPayload;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Wishlist\Handler\Command\DestroyWishlistHandlerContract,
    Segment\Wishlist\Service\WishlistCommandContract
};

final readonly class DestroyWishlistHandler implements DestroyWishlistHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param WishlistCommandContract $wishlistCommand
    */
    public function __construct(
        private SecurityPolicyContract $securityPolicy,
        private WishlistCommandContract $wishlistCommand,
    ) {}

    /**
     * @param WishlistPayload $payload
     *
     * @return bool
    */
    public function handle(WishlistPayload $payload): bool
    {
        $user = UserAssertion::assertInstance(
            $this->securityPolicy->checkIfEmailVerified(),
        );

        return $this->wishlistCommand->remove($user, $payload->variantId);
    }
}
