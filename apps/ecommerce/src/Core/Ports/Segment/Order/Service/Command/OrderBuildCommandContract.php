<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\Entity\Order,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\User\Entity\User
};

interface OrderBuildCommandContract
{
    /** @param CartItem[] $items */
    public function build(User $user, OrderCreatePayload $payload, array $items): Order;
}
