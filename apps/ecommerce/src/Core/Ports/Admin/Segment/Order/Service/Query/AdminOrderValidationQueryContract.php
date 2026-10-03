<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Order\Service\Query;

use App\Core\Domain\{
    Admin\Order\AdminOrderStatusPayload,
    Segment\Order\Entity\Order
};

interface AdminOrderValidationQueryContract
{
    public function checkSameStatus(Order $order, AdminOrderStatusPayload $payload): void;
    public function checkRefundedStatus(Order $order): void;
    public function checkCompletedStatus(Order $order, AdminOrderStatusPayload $payload): void;
}
