<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Query;

use Kit\{
    Assertion\Domain\Cart\CartAssertion,
    Assertion\Shared\IdAssertion
};

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Cart\ValueObject\CartItemObject,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Specification\ProductVariantAvailabilitySpecification,
    Segment\Product\Utils\ProductToolkit
};

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartSummaryQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

use App\Shared\Utils\Formatter\PriceFormatter;

/**
 * @phpstan-import-type CartSummary from CartSummaryQueryContract
 * @phpstan-import-type CartTotals from CartSummaryQueryContract
 * @phpstan-import-type CartResponse from CartSummaryQueryContract
*/
final readonly class CartSummaryQueryService implements CartSummaryQueryContract
{
    /**
     * @param CartRepositoryContract $cartRepository
     * @param ReviewQueryContract $reviewQuery
    */
    public function __construct(
        private CartRepositoryContract $cartRepository,
        private ReviewQueryContract $reviewQuery,
    ) {}

    /**
     * @param int $userId
     *
     * @return CartSummary
    */
    public function getCartSummary(int $userId): array
    {
        $cart = $this->cartRepository->findCartForUser($userId);
        if ($cart === null) {
            return $this->getEmptyCartSummary();
        }

        $items = $cart->getItems()->toArray();
        $filteredItems = [];
        $totalPrice = 0.0;

        foreach ($items as $item) {
            $variant = $item->getVariant();

            if (!$this->hasAvailableStock($variant)) {
                continue;
            }

            $totalPrice += $variant->getDiscountedPrice();
            $filteredItems[] = $this->buildCartItemObject($item, $variant);
        }

        return [
            'items'      => $filteredItems,
            'totalPrice' => $totalPrice,
            'totalItems' => count($filteredItems),
        ];
    }

    /**
     * @param int $userId
     *
     * @return CartItemObject[]
    */
    public function findCartItemsForUser(int $userId): array
    {
        $summary = $this->getCartSummary($userId);

        return $summary['items'];
    }

    /**
     * @param string $message
     * @param string $html
     * @param CartTotals $summary
     *
     * @return CartResponse
    */
    public function buildSuccessResponse(string $message, string $html, array $summary): array
    {
        return $this->formatCartResponse($message, $html, $summary);
    }

    /**
     * @param string $message
     * @param string $html
     * @param CartTotals $summary
     * @param int $status
     *
     * @return CartResponse
    */
    public function buildErrorResponse(string $message, string $html, array $summary, int $status): array
    {
        return $this->formatCartResponse($message, $html, $summary, true, $status);
    }

    /**
     * @return CartSummary
    */
    private function getEmptyCartSummary(): array
    {
        return [
            'items'      => [],
            'totalPrice' => 0.0,
            'totalItems' => 0,
        ];
    }

    /**
     * @param ProductVariant $variant
     *
     * @return bool
    */
    private function hasAvailableStock(ProductVariant $variant): bool
    {
        return ProductVariantAvailabilitySpecification::findOneInStock($variant) !== null;
    }

    /**
     * @param CartItem $item
     * @param ProductVariant $variant
     *
     * @return CartItemObject
     *
     * @throws \LogicException
    */
    private function buildCartItemObject(CartItem $item, ProductVariant $variant): CartItemObject
    {
        $id = IdAssertion::assert(
            $item->getId(),
            'CartItem ID',
            \LogicException::class,
        );

        $cart = $item->getCart();
        CartAssertion::assertExists($cart);

        return new CartItemObject(
            id: $id,
            cartId: $cart->getId(),
            variant: $variant,
            formattedPrice: PriceFormatter::format($variant->getPrice()),
            hasDiscount: $variant->getDiscount() !== null,
            imagePath: ProductToolkit::getFirstImagePath($variant),
            averageRating: $this->reviewQuery->getAverageRatingByVariant($variant->getId()),
            discountPrice: $variant->getDiscount()?->getPrice(),
            formattedDiscountPrice: $this->formatDiscountPrice($variant),
        );
    }

    /**
     * @param ProductVariant $variant
     *
     * @return string|null
    */
    private function formatDiscountPrice(ProductVariant $variant): ?string
    {
        if ($variant->getDiscount() === null) {
            return null;
        }

        return PriceFormatter::format($variant->getDiscountedPrice());
    }

    /**
     * @param string $message
     * @param string $html
     * @param CartTotals $summary
     * @param bool $isError
     * @param int|null $status
     *
     * @return CartResponse
    */
    private function formatCartResponse(
        string $message,
        string $html,
        array $summary,
        bool $isError = false,
        ?int $status = null,
    ): array {
        return [
            'html'       => $html,
            'totalItems' => $summary['totalItems'],
            'totalPrice' => $summary['totalPrice'],
            'message'    => $message,
            'success'    => !$isError,
            'status'     => $status,
        ];
    }
}
