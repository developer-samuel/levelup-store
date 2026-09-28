<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use Kit\{
    Assertion\Domain\Order\OrderBillingAssertion,
    Assertion\Domain\Order\OrderPersonalAssertion
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\Order\Event\OrderConfirmationRequestedEvent,
    Segment\Order\ValueObject\Email\OrderItemEmailObject,
    Segment\Order\ValueObject\Email\OrderVariantEmailObject
};

use App\Core\Ports\{
    Segment\Order\Notifier\OrderConfirmationNotifierContract,
    Segment\Order\Repository\OrderBillingRepositoryContract,
    Segment\Order\Repository\OrderItemRepositoryContract,
    Segment\Order\Repository\OrderPaymentRepositoryContract,
    Segment\Order\Repository\OrderPersonalRepositoryContract,
    Segment\Order\Repository\OrderRepositoryContract,
    Segment\Order\Repository\OrderShippingRepositoryContract
};

final readonly class OrderConfirmationNotifier implements OrderConfirmationNotifierContract
{
    /**
     * @param EventDispatcherInterface $dispatcher
     * @param OrderRepositoryContract $orderRepository
     * @param OrderItemRepositoryContract $orderItemRepository
     * @param OrderPaymentRepositoryContract $orderPaymentRepository
     * @param OrderPersonalRepositoryContract $orderPersonalRepository
     * @param OrderBillingRepositoryContract $orderBillingRepository
     * @param OrderShippingRepositoryContract $orderShippingRepository
    */
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        public OrderRepositoryContract $orderRepository,
        public OrderItemRepositoryContract $orderItemRepository,
        public OrderPaymentRepositoryContract $orderPaymentRepository,
        public OrderPersonalRepositoryContract $orderPersonalRepository,
        public OrderBillingRepositoryContract $orderBillingRepository,
        public OrderShippingRepositoryContract $orderShippingRepository,
    ) {}

    /**
     * @param Order $order
     *
     * @return void
    */
    public function send(Order $order): void
    {
        $personal = $this->orderPersonalRepository->findOneByOrder($order);
        OrderPersonalAssertion::assertExists($personal);

        $billing = $this->orderBillingRepository->findOneByOrder($order);
        OrderBillingAssertion::assertExists($billing);

        $shipping = $this->orderShippingRepository->findOneByOrder($order);
        $items = $this->orderItemRepository->findByOrder($order);

        $formattedItems = $this->getFormattedItems($items);

        $event = new OrderConfirmationRequestedEvent(
            $order,
            $personal,
            $billing,
            $shipping,
            $formattedItems,
        );

        $this->dispatcher->dispatch($event);
    }

    /**
     * @param OrderItem[] $items
     *
     * @return OrderItemEmailObject[]
    */
    private function getFormattedItems(array $items): array
    {
        return array_values(
            array_map(
                fn(OrderItem $item): OrderItemEmailObject =>
                    $this->createEmailObject($item),
                $items,
            ),
        );
    }

    /**
     * @param OrderItem $item
     *
     * @return OrderItemEmailObject
    */
    private function createEmailObject(OrderItem $item): OrderItemEmailObject
    {
        return new OrderItemEmailObject(
            variant: $this->createEmailVariantObject($item),
            price: $item->getPrice(),
            imagePath: $item->getVariant()->getImage()?->getPath(),
        );
    }

    /**
     * @param OrderItem $item
     *
     * @return OrderVariantEmailObject
    */
    private function createEmailVariantObject(OrderItem $item): OrderVariantEmailObject
    {
        $variant = $item->getVariant();

        return new OrderVariantEmailObject(
            name: $variant->getName(),
            url: $variant->getUrl(),
        );
    }
}
