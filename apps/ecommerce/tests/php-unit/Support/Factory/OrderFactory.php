<?php

declare(strict_types=1);

namespace Tests\Support\Factory;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\User\Entity\User
};

trait OrderFactory
{
    private function createAndPersistOrder(
        User $user,
        string $code,
        OrderStatus $status = OrderStatus::PROCESSED,
    ): Order {
        $order = (new Order())
            ->setUser($user)
            ->setCode($code)
            ->setPrice(99.99)
            ->setPayment(OrderPaymentMethod::CARD)
            ->setStatus($status);

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    private function createAndPersistOrderItem(
        Order $order,
        ProductVariant $variant,
        ProductVariantEan $ean,
        float $price = 99.99,
    ): OrderItem {
        $item = (new OrderItem())
            ->setOrder($order)
            ->setVariant($variant)
            ->setEan($ean)
            ->setPrice($price);

        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }
}
