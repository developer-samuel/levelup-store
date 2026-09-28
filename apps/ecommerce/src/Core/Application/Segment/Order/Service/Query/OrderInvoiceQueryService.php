<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use Kit\Assertion\Domain\Order\{
    OrderAssertion,
    OrderBillingAssertion,
    OrderPersonalAssertion
};

use App\Core\Domain\Segment\Order\Entity\Order;

use App\Core\Application\Segment\Order\Resource\OrderInvoiceResource;

use App\Core\Ports\{
    Segment\Order\Repository\OrderRepositoryContract,
    Segment\Order\Service\Query\OrderInvoiceQueryContract
};

final readonly class OrderInvoiceQueryService implements OrderInvoiceQueryContract
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
     * @return array<string, mixed>
    */
    public function getInvoiceDetails(string $code): array
    {
        $order = $this->orderRepository->getOrderByCode($code);
        OrderAssertion::assertExists($order);

        $this->assertOrderIntegrity($order);

        return OrderInvoiceResource::toArray($order);
    }

    /**
     * @param Order $order
     *
     * @return void
     *
     * @throws \LogicException
    */
    private function assertOrderIntegrity(Order $order): void
    {
        OrderPersonalAssertion::assertExists($order->getPersonal());
        OrderBillingAssertion::assertExists($order->getBilling());

        if ($order->getSendShipping() && $order->getShipping() === null) {
            throw new \LogicException('Shipping address is missing.');
        }

        if ($order->getItems()->isEmpty()) {
            throw new \LogicException('Order has no products.');
        }
    }
}
