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
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param CartControlQueryContract $cartControlQuery
     * @param CartControlCommandContract $cartControlCommand
     * @param CartItemCommandContract $cartItemCommand
    */
    public function __construct(
        private SecurityPolicyContract $securityPolicy,
        private CartControlQueryContract $cartControlQuery,
        private CartControlCommandContract $cartControlCommand,
        private CartItemCommandContract $cartItemCommand,
    ) {}

    /**
     * @param int $variantId
     *
     * @return array<string, mixed>
    */
    public function addToCart(int $variantId): array
    {
        return $this->updateCart($variantId, CartAction::ADD);
    }

    /**
     * @param int $itemId
     *
     * @return array<string, mixed>
    */
    public function removeFromCart(int $itemId): array
    {
        return $this->updateCart($itemId, CartAction::REMOVE);
    }

    /**
     * @param int $itemId
     * @param CartAction $action
     *
     * @return array<string, mixed>
    */
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

    /**
     * @param User $user
     *
     * @return void
    */
    private function ensureUserHasCart(User $user): void
    {
        $cart = $this->cartControlQuery->getUserCart($user);
        if ($cart === null) {
            $this->cartControlCommand->createNewCart($user);
        }
    }
}
