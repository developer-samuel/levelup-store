<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Order;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Order\Entity\OrderPersonal;

final class OrderPersonalAssertion
{
    /** @phpstan-assert OrderPersonal $personal */
    public static function assertExists(?OrderPersonal $personal): void
    {
        ExistenceAssertion::assertExists($personal, 'Order Personal');
    }
}
