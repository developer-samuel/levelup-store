<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Resource;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus
};

use App\Core\Application\{
    Segment\Order\Mapper\OrderAddressMapper,
    Segment\Order\Mapper\OrderPersonalMapper
};

/**
 * @phpstan-import-type AddressSnake from OrderAddressMapper
 * @phpstan-import-type PersonalSnake from OrderPersonalMapper
 *
 * @phpstan-type InvoiceProduct array{
 *     variant_id: int,
 *     name: string,
 *     unitPrice: float,
 *     quantity: int,
 *     totalPrice: float
 * }
 * @phpstan-type OrderSummary array{
 *     code: string,
 *     status: OrderStatus,
 *     payment: OrderPaymentMethod,
 *     price: float,
 *     hasPayment: bool
 * }
*/
final class OrderInvoiceResource
{
    /**
     * @param Order $order
     *
     * @return array{
     *     order: OrderSummary,
     *     personal: PersonalSnake,
     *     billing: AddressSnake,
     *     shipping: AddressSnake,
     *     hasShipping: bool,
     *     products: list<InvoiceProduct>
     * }
     *
    */
    public static function toArray(Order $order): array
    {
        return [
            'order'       => self::orderData($order),
            'personal'    => OrderPersonalMapper::mapToSnakeCase($order),
            'billing'     => OrderAddressMapper::mapBillingSnakeCase($order),
            'shipping'    => OrderAddressMapper::mapShippingSnakeCase($order),
            'hasShipping' => $order->getSendShipping(),
            'products'    => self::productsData($order),
        ];
    }

    /**
     * @param Order $order
     *
     * @return OrderSummary
    */
    private static function orderData(Order $order): array
    {
        return [
            'code'       => $order->getCode(),
            'status'     => $order->getStatus(),
            'payment'    => $order->getPayment(),
            'price'      => $order->getPrice(),
            'hasPayment' => $order->hasPayment(),
        ];
    }

    /**
     * @return list<InvoiceProduct>
    */
    private static function productsData(Order $order): array
    {
        $grouped = [];

        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            $variantId = $variant->getId();
            $unitPrice = $item->getPrice();

            if (!isset($grouped[$variantId])) {
                $grouped[$variantId] = [
                    'variant_id' => $variantId,
                    'name'       => $variant->getName(),
                    'unitPrice'  => $unitPrice,
                    'quantity'   => 0,
                    'totalPrice' => 0.0,
                ];
            }

            $grouped[$variantId]['quantity']++;
            $grouped[$variantId]['totalPrice'] += $unitPrice;
        }

        return array_values($grouped);
    }
}
