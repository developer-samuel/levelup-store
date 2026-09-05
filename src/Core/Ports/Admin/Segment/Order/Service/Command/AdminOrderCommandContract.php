<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Order\Service\Command;

use App\Core\Domain\{
    Admin\Order\AdminOrderStatusPayload,
    Segment\Order\Entity\Order
};

interface AdminOrderCommandContract
{
    /**
     * @param Order $order
     * @param AdminOrderStatusPayload $payload
     *
     * @return void
    */
    public function updateOrderStatus(Order $order, AdminOrderStatusPayload $payload): void;
}
