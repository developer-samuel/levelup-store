<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Cart\Enum\CartAction,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Cart\Policy\CartItemAvailabilityPolicyContract,
    Segment\Cart\Service\Command\CartControlCommandContract,
    Segment\Cart\Service\Command\CartItemCommandContract,
    Segment\Cart\Service\Query\CartItemQueryContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class CartItemCommandService implements CartItemCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param CartItemAvailabilityPolicyContract $cartItemPolicy
     * @param CartItemQueryContract $cartItemQuery
     * @param CartRenderQueryContract $cartRenderQuery
     * @param CartControlCommandContract $cartControlCommand
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private CartItemAvailabilityPolicyContract $cartItemPolicy,
        private CartItemQueryContract $cartItemQuery,
        private CartRenderQueryContract $cartRenderQuery,
        private CartControlCommandContract $cartControlCommand,
    ) {}

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return array<string, mixed>
    */
    public function addProductToCart(User $user, int $variantId): array
    {
        $itemData = $this->cartItemQuery->getCartAndVariant($user, $variantId);

        $cart = $itemData['cart'];
        $variant = $itemData['variant'];

        if (!$this->cartItemPolicy->isAvailable($cart, $variant)) {
            return $this->cartRenderQuery->buildCartResponse(
                $user,
                'This product is no longer in stock.',
                true,
            );
        }

        $item = new CartItem($cart, $variant);

        $this->entityPersistence->persist($item, true);

        $this->refreshCart($cart);

        return $this->cartItemQuery->buildCartResponse($user, CartAction::ADD);
    }

    /**
     * @param User $user
     * @param int $itemId
     *
     * @return array<string, mixed>
    */
    public function removeProductFromCart(User $user, int $itemId): array
    {
        $item = $this->cartItemQuery->getValidatedCartItem($itemId);
        $cart = $item->getCart();

        $this->entityPersistence->remove($item);

        $this->refreshCartIfExists($cart);

        return $this->cartItemQuery->buildCartResponse($user, CartAction::REMOVE);
    }

    /**
     * @param ProductVariant $variant
     * @param CartItem[] $cartItems
     *
     * @return void
    */
    public function removeVariant(ProductVariant $variant, array $cartItems): void
    {
        $cart = null;

        foreach ($cartItems as $cartItem) {
            if ($cartItem->hasVariant($variant)) {
                $cart = $cart ?? $cartItem->getCart();
                $this->entityPersistence->remove($cartItem);
            }
        }

        $this->entityPersistence->flush();

        if ($cart !== null) {
            $this->cartControlCommand->flushAndRefreshCart($cart);
        }
    }

    /**
     * @param Cart $cart
     *
     * @return void
    */
    private function refreshCart(Cart $cart): void
    {
        $this->cartControlCommand->flushAndRefreshCart($cart);
    }

    /**
     * @param Cart|null $cart
     *
     * @return void
    */
    private function refreshCartIfExists(?Cart $cart): void
    {
        if ($cart !== null) {
            $this->refreshCart($cart);
        }
    }
}
