<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Order;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Order\Entity\OrderPersonal;

final class OrderPersonalAssertion
{
    /**
     * @param OrderPersonal|null $personal
     *
     * @return void
     *
     * @throws \RuntimeException
     *
     * @phpstan-assert OrderPersonal $personal
    */
    public static function assertExists(?OrderPersonal $personal): void
    {
        ExistenceAssertion::assertExists($personal, 'Order Personal');
    }
}
