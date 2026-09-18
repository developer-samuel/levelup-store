<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Query;

use Kit\{
    Assertion\Domain\Cart\CartAssertion,
    Assertion\Domain\Cart\CartItemAssertion,
    Assertion\Domain\Product\Variant\ProductVariantAssertion
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Cart\Enum\CartAction,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Cart\Repository\CartItemRepositoryContract,
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartControlQueryContract,
    Segment\Cart\Service\Query\CartItemQueryContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract
};

/**
 * @phpstan-import-type CartAndVariant from CartItemQueryContract
*/
final readonly class CartItemQueryService implements CartItemQueryContract
{
    /**
     * @param ProductVariantRepositoryContract $variantRepository
     * @param ProductVariantEanRepositoryContract $variantEanRepository
     * @param CartRepositoryContract $cartRepository
     * @param CartControlQueryContract $cartControlQuery
     * @param CartItemRepositoryContract $cartItemRepository
     * @param CartRenderQueryContract $cartRenderQuery
    */
    public function __construct(
        private ProductVariantRepositoryContract $variantRepository,
        private ProductVariantEanRepositoryContract $variantEanRepository,
        private CartRepositoryContract $cartRepository,
        private CartControlQueryContract $cartControlQuery,
        private CartItemRepositoryContract $cartItemRepository,
        private CartRenderQueryContract $cartRenderQuery,
    ) {}

    /**
     * @param User $user
     *
     * @return CartItem[]
    */
    public function getItems(User $user): array
    {
        $userId = $user->getId();

        $cart = $this->cartRepository->findCartForUser($userId);

        return $this->extractCartItems($cart);
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return CartAndVariant
    */
    public function getCartAndVariant(User $user, int $variantId): array
    {
        $variant = $this->getVariant($variantId);
        ProductVariantAssertion::assertExists($variant);

        $cart = $this->cartControlQuery->getUserCart($user);
        CartAssertion::assertExists($cart);

        return [
            'cart'    => $cart,
            'variant' => $variant,
        ];
    }

    /**
     * @param int $itemId
     *
     * @return CartItem
    */
    public function getValidatedCartItem(int $itemId): CartItem
    {
        $item = $this->getItem($itemId);
        CartItemAssertion::assertExists($item);

        return $item;
    }

    /**
     * @param ProductVariant $variant
     *
     * @return int
    */
    public function getAvailableEansCount(ProductVariant $variant): int
    {
        $availableEans = $this->variantEanRepository->findAvailableByVariant($variant);

        if ($availableEans === []) {
            return 0;
        }

        return count($availableEans);
    }

    /**
     * @param Cart $cart
     * @param ProductVariant $variant
     *
     * @return int
    */
    public function getExistingQuantity(Cart $cart, ProductVariant $variant): int
    {
        $items = $cart->getItems();
        $quantity = 0;

        foreach ($items as $item) {
            if ($item->hasVariant($variant)) {
                $quantity++;
            }
        }

        return $quantity;
    }

    /**
     * @param User $user
     * @param CartAction $action
     *
     * @return array<string, mixed>
    */
    public function buildCartResponse(User $user, CartAction $action): array
    {
        return $this->cartRenderQuery->buildCartResponse(
            $user,
            $action->successMessage(),
        );
    }

    /**
     * @param Cart|null $cart
     *
     * @return CartItem[]
    */
    private function extractCartItems(?Cart $cart): array
    {
        if ($cart === null) {
            return [];
        }

        return $cart->getItems()->toArray();
    }

    /**
     * @param int $variantId
     *
     * @return ProductVariant|null
    */
    private function getVariant(int $variantId): ?ProductVariant
    {
        return $this->variantRepository->findById($variantId);
    }

    /**
     * @param int $itemId
     *
     * @return CartItem|null
    */
    private function getItem(int $itemId): ?CartItem
    {
        return $this->cartItemRepository->getItem($itemId);
    }
}
