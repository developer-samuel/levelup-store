<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Order;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Order\Entity\Order;

final class OrderAssertion
{
    /** @phpstan-assert Order $order */
    public static function assertExists(?Order $order): void
    {
        ExistenceAssertion::assertExists($order, 'Order');
    }

    public static function assertOrderCode(?string $code): void
    {
        if ($code === null) {
            throw new \InvalidArgumentException('Order code cannot be null.');
        }
    }
}
