<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Order;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling
};

final class OrderBillingAssertion
{
    /**
     * @param OrderBilling|null $billing
     *
     * @return void
     *
     * @throws \RuntimeException
     *
     * @phpstan-assert OrderBilling $billing
    */
    public static function assertExists(?OrderBilling $billing): void
    {
        ExistenceAssertion::assertExists($billing, 'Order Billing');
    }

    /**
     * @param Order $order
     *
     * @return OrderBilling
     *
     * @throws \InvalidArgumentException
    */
    public static function assertBillingExists(Order $order): OrderBilling
    {
        $billing = $order->getBilling();
        if (!$billing instanceof OrderBilling) {
            throw new \InvalidArgumentException('Billing address is required');
        }

        return $billing;
    }
}
