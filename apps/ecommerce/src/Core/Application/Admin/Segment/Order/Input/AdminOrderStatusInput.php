<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Order\Input;

use App\Core\Domain\Segment\Order\Enum\OrderStatus;

use App\Core\Application\Shared\Constraint\NotBlankConstraint;

trait AdminOrderStatusInput
{
    #[NotBlankConstraint('Code')]
    public string $code;

    #[NotBlankConstraint('Order status')]
    public OrderStatus $status;
}
