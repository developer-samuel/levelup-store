<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\Order\ValueObject\OrderItemObject,
    Segment\Product\Utils\ProductToolkit
};

use App\Core\Ports\{
    Segment\Order\Repository\OrderRepositoryContract,
    Segment\Order\Service\Query\OrderDetailQueryContract
};

/**
 * @phpstan-import-type ItemsWithTotal from OrderDetailQueryContract
*/
final class OrderDetailQueryService implements OrderDetailQueryContract
{
    /**
     * @param OrderRepositoryContract $orderRepository
    */
    public function __construct(
        private OrderRepositoryContract $orderRepository,
    ) {}

    /**
     * @param string $code
     *
     * @return Order|null
    */
    public function fetchOrder(string $code): ?Order
    {
        return $this->orderRepository->findOne(['code' => $code]);
    }

    /**
     * @param Order $order
     *
     * @return ItemsWithTotal
    */
    public function buildItemsWithTotal(Order $order): array
    {
        $itemsViewData = [];
        $totalPrice = 0.0;

        foreach ($order->getItems() as $item) {
            $result = $this->mapItemToView($item);
            $itemsViewData[] = $result;

            $totalPrice += $result->price;
        }

        return [
            'items' => $itemsViewData,
            'total' => $totalPrice,
        ];
    }

    /**
     * @param OrderItem $item
     *
     * @return OrderItemObject
    */
    private function mapItemToView(OrderItem $item): OrderItemObject
    {
        $variant = $item->getVariant();
        $price = $item->getPrice();

        return new OrderItemObject(
            variant: $variant,
            imagePath: ProductToolkit::getFirstImagePath($variant),
            url: $variant->getUrl(),
            price: $price,
        );
    }
}
