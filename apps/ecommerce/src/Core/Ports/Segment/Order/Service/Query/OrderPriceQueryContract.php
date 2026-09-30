<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\Segment\Order\ValueObject\Stripe\StripeLineItemObject;

interface OrderPriceQueryContract
{
    /**
     * @param StripeLineItemObject[] $lineItems
     *
     * @return float
    */
    public function calculateTotalPrice(array $lineItems): float;
}
