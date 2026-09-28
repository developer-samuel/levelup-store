<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Kit\Assertion\Domain\Product\Variant\ProductVariantAssertion;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemPriceObject,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock
};

use App\Core\Ports\Segment\Order\Service\Query\OrderItemQueryContract;

/**
 * @phpstan-type VariantQuantity array{
 *     variant: ProductVariant,
 *     quantity: int
 * }
*/
final class OrderItemQueryService implements OrderItemQueryContract
{
    /**
     * @param CartItem[] $items
     *
     * @return StripeLineItemObject[]
    */
    public function prepareLineItems(array $items): array
    {
        $groupedItems = $this->groupItemsByVariant($items);

        return $this->mapGroupedItemsToLineItems($groupedItems);
    }

    /**
     * @param ProductVariantStock|null $stock
     *
     * @return bool
    */
    public function isStockAvailable(?ProductVariantStock $stock): bool
    {
        return $stock?->isAvailable() ?? false;
    }

    /**
     * @param CartItem[] $items
     *
     * @return array<int, VariantQuantity>
    */
    private function groupItemsByVariant(array $items): array
    {
        $groupedItems = [];

        foreach ($items as $cartItem) {
            $variant = $cartItem->getVariant();

            $variantId = $variant->getId();

            if (isset($groupedItems[$variantId])) {
                $groupedItems[$variantId]['quantity']++;
                continue;
            }

            $groupedItems[$variantId] = [
                'variant'  => $variant,
                'quantity' => 1,
            ];
        }

        return $groupedItems;
    }

    /**
     * @param array<int, VariantQuantity> $groupedItems
     *
     * @return StripeLineItemObject[]
    */
    private function mapGroupedItemsToLineItems(array $groupedItems): array
    {
        return array_map(
            fn(array $item): StripeLineItemObject => $this->buildLineItem($item['variant'], $item['quantity']),
            $groupedItems,
        );
    }

    /**
     * @param ProductVariant $variant
     * @param int $quantity
     *
     * @return StripeLineItemObject
     *
     * @throws NotFoundHttpException
    */
    private function buildLineItem(ProductVariant $variant, int $quantity): StripeLineItemObject
    {
        ProductVariantAssertion::assertNameExists($variant);

        $product = $variant->getProduct();
        $formattedPrice = $variant->getDiscountedPrice();

        $price = $this->createItemPrice($product->getName(), $formattedPrice);

        return $this->createLineItem($price, $quantity);
    }

    /**
     * @param string $productName
     * @param float $formattedPrice
     *
     * @return StripeLineItemPriceObject
    */
    private function createItemPrice(string $productName, float $formattedPrice): StripeLineItemPriceObject
    {
        return new StripeLineItemPriceObject(
            currency: 'eur',
            productName: $productName,
            unitAmount: (int) ($formattedPrice * 100),
        );
    }

    /**
     * @param StripeLineItemPriceObject $price
     * @param int $quantity
     *
     * @return StripeLineItemObject
    */
    private function createLineItem(StripeLineItemPriceObject $price, int $quantity): StripeLineItemObject
    {
        return new StripeLineItemObject(
            price: $price,
            quantity: $quantity,
        );
    }
}
