<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Enum\CartAction,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Cart\Service\Command\CartControlCommandContract,
    Segment\Cart\Service\Command\CartItemCommandContract,
    Segment\Cart\Service\Command\CartMutationCommandContract,
    Segment\Cart\Service\Query\CartControlQueryContract
};

final readonly class CartMutationCommandService implements CartMutationCommandContract
{
    public function __construct(
        private SecurityPolicyContract $securityPolicy,
        private CartControlQueryContract $cartControlQuery,
        private CartControlCommandContract $cartControlCommand,
        private CartItemCommandContract $cartItemCommand,
    ) {}

    /** @return array<string, mixed> */
    public function addToCart(int $variantId): array
    {
        return $this->updateCart($variantId, CartAction::ADD);
    }

    /** @return array<string, mixed> */
    public function removeFromCart(int $itemId): array
    {
        return $this->updateCart($itemId, CartAction::REMOVE);
    }

    /** @return array<string, mixed> */
    private function updateCart(int $itemId, CartAction $action): array
    {
        $user = $this->securityPolicy->checkIfEmailVerified();

        if ($action === CartAction::ADD) {
            $this->ensureUserHasCart($user);
        }

        return match ($action) {
            CartAction::ADD    => $this->cartItemCommand->addProductToCart($user, $itemId),
            CartAction::REMOVE => $this->cartItemCommand->removeProductFromCart($user, $itemId),
        };
    }

    private function ensureUserHasCart(User $user): void
    {
        $cart = $this->cartControlQuery->getUserCart($user);
        if ($cart === null) {
            $this->cartControlCommand->createNewCart($user);
        }
    }
}
