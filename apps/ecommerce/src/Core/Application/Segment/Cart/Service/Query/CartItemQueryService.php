<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Query;

use Packages\Kit\{
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
    public function __construct(
        private ProductVariantRepositoryContract $variantRepository,
        private ProductVariantEanRepositoryContract $variantEanRepository,
        private CartRepositoryContract $cartRepository,
        private CartControlQueryContract $cartControlQuery,
        private CartItemRepositoryContract $cartItemRepository,
        private CartRenderQueryContract $cartRenderQuery,
    ) {}

    /** @return CartItem[] */
    public function getItems(User $user): array
    {
        $userId = $user->getId();

        $cart = $this->cartRepository->findCartForUser($userId);

        return $this->extractCartItems($cart);
    }

    /** @return CartAndVariant */
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

    public function getValidatedCartItem(int $itemId): CartItem
    {
        $item = $this->getItem($itemId);
        CartItemAssertion::assertExists($item);

        return $item;
    }

    public function getAvailableEansCount(ProductVariant $variant): int
    {
        $availableEans = $this->variantEanRepository->findAvailableByVariant($variant);

        if ($availableEans === []) {
            return 0;
        }

        return count($availableEans);
    }

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

    /** @return array<string, mixed> */
    public function buildCartResponse(User $user, CartAction $action): array
    {
        return $this->cartRenderQuery->buildCartResponse(
            $user,
            $action->successMessage(),
        );
    }

    /** @return CartItem[] */
    private function extractCartItems(?Cart $cart): array
    {
        if ($cart === null) {
            return [];
        }

        return $cart->getItems()->toArray();
    }

    private function getVariant(int $variantId): ?ProductVariant
    {
        return $this->variantRepository->findById($variantId);
    }

    private function getItem(int $itemId): ?CartItem
    {
        return $this->cartItemRepository->getItem($itemId);
    }
}
