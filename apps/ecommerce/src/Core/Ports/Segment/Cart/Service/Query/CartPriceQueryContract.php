<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Query;

use App\Core\Domain\Segment\Cart\ValueObject\CartItemObject;

interface CartPriceQueryContract
{
    /** @param CartItemObject[] $items */
    public function calculateTotalPrice(array $items): float;
}
