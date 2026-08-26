<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Projection;

use App\Core\Domain\Segment\Order\Entity\Order;

use App\Core\Ports\{
    Gateways\External\Search\ElasticsearchGatewayContract,
    Segment\Order\Repository\OrderRepositoryContract,
    Shared\ReindexableInterface
};

final readonly class OrderProjector implements ReindexableInterface
{
    /**
     * @param ElasticsearchGatewayContract $elasticsearch
     * @param OrderRepositoryContract $orderRepository
    */
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
        private OrderRepositoryContract $orderRepository,
    ) {}

    /**
     * @param Order $order
     *
     * @return void
    */
    public function index(Order $order): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->indexDocument(OrderProjection::NAME, $order->getId(), $this->buildDocument($order));
    }

    /**
     * @param Order $order
     *
     * @return void
    */
    public function remove(Order $order): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(OrderProjection::NAME, $order->getId());
    }

    /**
     * @return int
    */
    public function reindexAll(): int
    {
        $this->elasticsearch->ensureIndexExists(OrderProjection::NAME, OrderProjection::mapping());

        $orders  = $this->orderRepository->findAll();
        $indexed = 0;

        foreach ($orders as $order) {
            $this->index($order);
            ++$indexed;
        }

        return $indexed;
    }

    /**
     * @return string
    */
    public function getIndexName(): string
    {
        return OrderProjection::NAME;
    }

    /**
     * @param Order $order
     *
     * @return array<string, mixed>
    */
    private function buildDocument(Order $order): array
    {
        return [
            'code'           => $order->getCode(),
            'status'         => $order->getStatus()->value,
            'payment_method' => $order->getPayment()->value,
            'price'          => $order->getPrice(),
            'has_payment'    => $order->hasPayment(),
            'send_shipping'  => $order->getSendShipping(),
            'user_id'        => $order->getUser()->getId(),
            'created_at'     => $order->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
