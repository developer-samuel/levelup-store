<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Order;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling
};

final class OrderBillingAssertion
{
    /** @phpstan-assert OrderBilling $billing */
    public static function assertExists(?OrderBilling $billing): void
    {
        ExistenceAssertion::assertExists($billing, 'Order Billing');
    }

    public static function assertBillingExists(Order $order): OrderBilling
    {
        $billing = $order->getBilling();
        if (!$billing instanceof OrderBilling) {
            throw new \InvalidArgumentException('Billing address is required');
        }

        return $billing;
    }
}
