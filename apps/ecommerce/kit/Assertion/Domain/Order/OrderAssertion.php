<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Order;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Order\Entity\Order;

final class OrderAssertion
{
    /**
     * @param Order|null $order
     *
     * @return void
     *
     * @throws \RuntimeException
     *
     * @phpstan-assert Order $order
    */
    public static function assertExists(?Order $order): void
    {
        ExistenceAssertion::assertExists($order, 'Order');
    }

    /**
     * @param string|null $code
     *
     * @return void
     *
     * @throws \InvalidArgumentException
    */
    public static function assertOrderCode(?string $code): void
    {
        if ($code === null) {
            throw new \InvalidArgumentException('Order code cannot be null.');
        }
    }
}
