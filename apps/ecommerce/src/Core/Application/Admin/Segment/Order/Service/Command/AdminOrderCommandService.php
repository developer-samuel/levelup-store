<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Order\Service\Command;

use Kit\Assertion\Domain\Product\Variant\ProductVariantStockAssertion;

use App\Core\Domain\{
    Admin\Order\AdminOrderStatusPayload,
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\Order\Entity\OrderPayment,
    Segment\Order\Enum\OrderStatus,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

use App\Core\Ports\{
    Admin\Segment\Order\Service\Command\AdminOrderCommandContract,
    Segment\Order\Notifier\OrderStatusNotifierContract,
    Segment\Order\Repository\OrderPaymentRepositoryContract,
    Segment\Order\Service\Command\OrderCacheCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class AdminOrderCommandService implements AdminOrderCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param OrderPaymentRepositoryContract $orderPaymentRepository
     * @param OrderCacheCommandContract $orderCacheCommand
     * @param OrderStatusNotifierContract $notifier
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private OrderPaymentRepositoryContract $orderPaymentRepository,
        private OrderCacheCommandContract $orderCacheCommand,
        private OrderStatusNotifierContract $notifier,
    ) {}

    /**
     * @param Order $order
     * @param AdminOrderStatusPayload $payload
     *
     * @return void
    */
    public function updateOrderStatus(Order $order, AdminOrderStatusPayload $payload): void
    {
        $newStatus = $payload->status;

        $this->updateOrderEntityStatus($order, $newStatus);
        $this->processOrderItems($order, $newStatus);
        $this->createPaymentIfNeeded($order, $newStatus);
        $this->saveAndDispatch($order);
    }

    /**
     * @param Order $order
     * @param OrderStatus $newStatus
     *
     * @return void
    */
    private function updateOrderEntityStatus(Order $order, OrderStatus $newStatus): void
    {
        $order->setStatus($newStatus)->setUpdatedAt();
    }

    /**
     * @param Order $order
     * @param OrderStatus $newStatus
     *
     * @return void
    */
    private function processOrderItems(Order $order, OrderStatus $newStatus): void
    {
        foreach ($order->getItems() as $item) {
            $this->processOrderItem($item, $newStatus);
        }
    }

    /**
     * @param OrderItem $item
     * @param OrderStatus $newStatus
     *
     * @return void
    */
    private function processOrderItem(OrderItem $item, OrderStatus $newStatus): void
    {
        $result = $this->getEanAndStock($item);

        $ean = $result['ean'];
        $stock = $result['stock'];

        $this->updateItemStatusAndStock($ean, $stock, $newStatus);
    }

    /**
     * @param Order $order
     * @param OrderStatus $newStatus
     *
     * @return void
    */
    private function createPaymentIfNeeded(Order $order, OrderStatus $newStatus): void
    {
        if ($newStatus === OrderStatus::COMPLETED) {
            $this->createPaymentForCashOrderIfNeeded($order);
        }
    }

    /**
     * @param Order $order
     *
     * @return void
    */
    private function saveAndDispatch(Order $order): void
    {
        $this->entityPersistence->persist($order, true);

        $this->notifier->send($order);

        $this->invalidateUserCache($order);
    }

    /**
     * @param OrderItem $item
     *
     * @return array{
     *     ean: ProductVariantEan,
     *     stock: ProductVariantStock
     * }
    */
    private function getEanAndStock(OrderItem $item): array
    {
        $ean = $item->getEan();
        $stock = $item->getVariant()->getStock();

        ProductVariantStockAssertion::assertExists($stock);

        return [
            'ean'   => $ean,
            'stock' => $stock,
        ];
    }

    /**
     * @param ProductVariantEan $ean
     * @param ProductVariantStock $stock
     * @param OrderStatus $status
    */
    private function updateItemStatusAndStock(ProductVariantEan $ean, ProductVariantStock $stock, OrderStatus $status): void
    {
        $stockChanged = $status === OrderStatus::COMPLETED || $status === OrderStatus::REFUNDED;

        match ($status) {
            OrderStatus::COMPLETED => $this->completeItem($ean, $stock),
            OrderStatus::REFUNDED  => $this->refundItem($ean, $stock),
            default                => null,
        };

        if ($stockChanged) {
            $stock->recalculateStatus();
            $this->persistEntities($ean, $stock);
        }
    }

    /**
     * @param ProductVariantEan $ean
     * @param ProductVariantStock $stock
     *
     * @return void
    */
    private function persistEntities(ProductVariantEan $ean, ProductVariantStock $stock): void
    {
        $this->entityPersistence->persist($ean);
        $this->entityPersistence->persist($stock);
    }

    /**
     * @param Order $order
     *
     * @return void
    */
    private function invalidateUserCache(Order $order): void
    {
        $user = $order->getUser();

        $this->orderCacheCommand->invalidateOrdersCache($user);
    }

    /**
     * @param ProductVariantEan $ean
     * @param ProductVariantStock $stock
     *
     * @return void
    */
    private function completeItem(ProductVariantEan $ean, ProductVariantStock $stock): void
    {
        $ean->setStatus(ProductVariantEanStatus::SOLD);
        $stock->markCompleted();
    }

    /**
     * @param ProductVariantEan $ean
     * @param ProductVariantStock $stock
     *
     * @return void
    */
    private function refundItem(ProductVariantEan $ean, ProductVariantStock $stock): void
    {
        $ean->setStatus(ProductVariantEanStatus::REFUNDED);
        $stock->markRefunded();
    }

    /**
     * @param Order $order
     *
     * @return void
    */
    private function createPaymentForCashOrderIfNeeded(Order $order): void
    {
        if ($this->hasExistingPayment($order) || !$order->isCashPayment()) {
            return;
        }

        $payment = $this->buildCashPayment($order);

        $this->entityPersistence->persist($payment);
    }

    /**
     * @param Order $order
     *
     * @return bool
    */
    private function hasExistingPayment(Order $order): bool
    {
        return $this->orderPaymentRepository->getByOrder($order) !== null;
    }

    /**
     * @param Order $order
     *
     * @return OrderPayment
    */
    private function buildCashPayment(Order $order): OrderPayment
    {
        return (new OrderPayment())
            ->setOrder($order)
            ->setTransactionUnique('cash_' . bin2hex(random_bytes(12)))
            ->setPrice($order->getPrice());
    }
}
