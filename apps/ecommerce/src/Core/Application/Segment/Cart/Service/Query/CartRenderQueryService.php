<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Query;

use Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\{
    Segment\Cart\ValueObject\CartItemObject,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Cart\Resource\CartSummaryResource;

use App\Core\Ports\{
    Segment\Cart\Renderer\CartRendererContract,
    Segment\Cart\Service\Query\CartPriceQueryContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Segment\Cart\Service\Query\CartSummaryQueryContract
};

/**
 * @phpstan-import-type ObjectArray from CartItemObject
*/
final readonly class CartRenderQueryService implements CartRenderQueryContract
{
    /**
     * @param CartSummaryQueryContract $cartSummaryQuery
     * @param CartPriceQueryContract $cartPriceQuery
     * @param CartRendererContract $cartRenderer
    */
    public function __construct(
        private CartSummaryQueryContract $cartSummaryQuery,
        private CartPriceQueryContract $cartPriceQuery,
        private CartRendererContract $cartRenderer,
    ) {}

    /**
     * @param User $user
     * @param string $message
     * @param bool $isError
     *
     * @return array<string, mixed>
    */
    public function buildCartResponse(User $user, string $message, bool $isError = false): array
    {
        $userId = $user->getId();
        $validatedUserId = IdAssertion::assert($userId, 'User ID');

        $items = array_values(
            $this->cartSummaryQuery->findCartItemsForUser($validatedUserId),
        );

        $itemsToView = $this->transformItemsToView($items);

        $totalPrice = $this->cartPriceQuery->calculateTotalPrice($items);

        $html = $this->cartRenderer->renderCart($itemsToView);

        $summary = CartSummaryResource::toArray($items, $totalPrice);

        if ($isError) {
            return $this->cartSummaryQuery->buildErrorResponse($message, $html, $summary, 422);
        }

        return $this->cartSummaryQuery->buildSuccessResponse($message, $html, $summary);
    }

    /**
     * @param CartItemObject[] $items
     *
     * @return array<int, ObjectArray>
    */
    private function transformItemsToView(array $items): array
    {
        return array_values(array_map(
            static fn(CartItemObject $item): array => $item->toArray(),
            $items,
        ));
    }
}
