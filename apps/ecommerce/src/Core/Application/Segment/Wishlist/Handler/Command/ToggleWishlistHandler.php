<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Wishlist\Handler\Command;

use Presets\Kit\Assertion\Domain\User\UserAssertion;

use App\Core\Domain\Segment\Wishlist\Payload\WishlistPayload;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Wishlist\Handler\Command\ToggleWishlistHandlerContract,
    Segment\Wishlist\Notifier\WishlistNotifierContract,
    Segment\Wishlist\Service\WishlistCommandContract
};

final readonly class ToggleWishlistHandler implements ToggleWishlistHandlerContract
{
    public function __construct(
        private SecurityPolicyContract $securityPolicy,
        private WishlistCommandContract $wishlistCommand,
        private WishlistNotifierContract $wishlistNotifier,
    ) {}

    public function handle(WishlistPayload $payload): bool
    {
        $user = UserAssertion::assertInstance(
            $this->securityPolicy->checkIfEmailVerified(),
        );

        $added = $this->wishlistCommand->toggle($user, $payload->variantId);

        $added
            ? $this->wishlistNotifier->notifyAdded($user, $payload->variantId)
            : $this->wishlistNotifier->notifyRemoved($user, $payload->variantId);

        return $added;
    }
}
