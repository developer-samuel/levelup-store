<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\Segment\Order\Payload\OrderCreatePayload;
use App\Core\Domain\Segment\Order\ValueObject\OrderResultObject;

interface OrderMutationCommandContract
{
    /**
     * @param OrderCreatePayload $payload
     *
     * @return OrderResultObject
    */
    public function createOrder(OrderCreatePayload $payload): OrderResultObject;
}
