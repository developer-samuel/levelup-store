<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Wishlist\Handler\Command;

use Packages\Kit\Assertion\Domain\User\UserAssertion;

use App\Core\Domain\Segment\Wishlist\Payload\WishlistPayload;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Wishlist\Handler\Command\ToggleWishlistHandlerContract,
    Segment\Wishlist\Service\WishlistCommandContract
};

final readonly class ToggleWishlistHandler implements ToggleWishlistHandlerContract
{
    public function __construct(
        private SecurityPolicyContract $securityPolicy,
        private WishlistCommandContract $wishlistCommand,
    ) {}

    public function handle(WishlistPayload $payload): bool
    {
        $user = UserAssertion::assertInstance(
            $this->securityPolicy->checkIfEmailVerified(),
        );

        return $this->wishlistCommand->toggle($user, $payload->variantId);
    }
}
