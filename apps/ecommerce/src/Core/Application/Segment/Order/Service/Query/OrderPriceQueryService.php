<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use App\Core\Domain\Segment\Order\ValueObject\Stripe\StripeLineItemObject;

use App\Core\Ports\Segment\Order\Service\Query\OrderPriceQueryContract;

final class OrderPriceQueryService implements OrderPriceQueryContract
{
    /**
     * @param StripeLineItemObject[] $lineItems
     *
     * @return float
    */
    public function calculateTotalPrice(array $lineItems): float
    {
        $totalPrice = 0.0;

        foreach ($lineItems as $item) {
            $totalPrice += ($item->price->unitAmount / 100) * $item->quantity;
        }

        return $totalPrice;
    }
}
