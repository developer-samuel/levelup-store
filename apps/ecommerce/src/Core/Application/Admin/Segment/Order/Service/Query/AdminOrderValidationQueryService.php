<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Order\Service\Query;

use App\Core\Domain\{
    Admin\Order\AdminOrderStatusPayload,
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderStatus,
};

use App\Core\Ports\Admin\Segment\Order\Service\Query\AdminOrderValidationQueryContract;

final class AdminOrderValidationQueryService implements AdminOrderValidationQueryContract
{
    /**
     * @param Order $order
     * @param AdminOrderStatusPayload $payload
     *
     * @return void
     *
     * @throws \DomainException
    */
    public function checkSameStatus(Order $order, AdminOrderStatusPayload $payload): void
    {
        if ($order->getStatus() === $payload->status) {
            throw new \DomainException(
                'The status is already set to the requested value.',
            );
        }
    }

    /**
     * @param Order $order
     *
     * @return void
     *
     * @throws \DomainException
    */
    public function checkRefundedStatus(Order $order): void
    {
        if ($order->getStatus() === OrderStatus::REFUNDED) {
            throw new \DomainException(
                'Refunded orders cannot be modified.',
            );
        }
    }

    /**
     * @param Order $order
     * @param AdminOrderStatusPayload $payload
     *
     * @return void
     *
     * @throws \DomainException
    */
    public function checkCompletedStatus(Order $order, AdminOrderStatusPayload $payload): void
    {
        if ($order->getStatus() === OrderStatus::COMPLETED && $payload->status !== OrderStatus::REFUNDED) {
            throw new \DomainException(
                'Completed orders can only be set to REFUNDED.',
            );
        }
    }
}
