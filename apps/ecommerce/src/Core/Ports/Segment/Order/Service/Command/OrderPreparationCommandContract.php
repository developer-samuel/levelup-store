<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\User\Entity\User
};

interface OrderPreparationCommandContract
{
    /**
     * @param User $user
     * @param OrderCreatePayload $payload
     *
     * @return Order
    */
    public function prepareOrder(User $user, OrderCreatePayload $payload): Order;
}
