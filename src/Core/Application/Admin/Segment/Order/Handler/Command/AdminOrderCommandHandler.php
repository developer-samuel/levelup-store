<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Order\Handler\Command;

use App\Core\Domain\{
    Admin\Segment\Order\Payload\AdminOrderStatusPayload,
    Segment\Audit\Enum\AuditAction,
    Segment\Order\Entity\Order
};

use App\Core\Application\Admin\Abstract\AbstractAdminFormCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Order\Service\Command\AdminOrderCommandContract,
    Admin\Segment\Order\Service\Query\AdminOrderValidationQueryContract,
    Security\Policy\SecurityPolicyContract,
    Segment\Audit\AuditLoggerContract,
    Segment\Order\Service\Query\OrderFetchQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

class AdminOrderCommandHandler extends AbstractAdminFormCommandHandler
{
    /**
     * @param OrderFetchQueryContract $orderFetchQuery
     * @param AdminOrderCommandContract $adminOrderCommand
     * @param AdminOrderValidationQueryContract $adminOrderValidationQuery
     * @param AuditLoggerContract $audit
     * @param SecurityPolicyContract $securityPolicy
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly OrderFetchQueryContract $orderFetchQuery,
        private readonly AdminOrderCommandContract $adminOrderCommand,
        private readonly AdminOrderValidationQueryContract $adminOrderValidationQuery,
        private readonly AuditLoggerContract $audit,
        SecurityPolicyContract $securityPolicy,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityPolicy,
            $logger,
        );
    }

    /**
     * @param AdminOrderStatusPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(AdminOrderStatusPayload $payload): array
    {
        return $this->executeAdmin(function() use ($payload) {
            $order = $this->orderFetchQuery->getOrderByCodeOrFail($payload->code);

            $this->assertStatusCanBeUpdated($order, $payload);

            $this->adminOrderCommand->updateOrderStatus($order, $payload);

            $this->audit->log(AuditAction::ORDER_STATUS_CHANGE, 'Order', $order->getId(), [
                'status' => $payload->status,
            ], $order->getUser());

            return ApiResultFormatter::success('Order status updated successfully.');
        });
    }

    /**
     * @param Order $order
     * @param AdminOrderStatusPayload $payload
     *
     * @return void
    */
    private function assertStatusCanBeUpdated(Order $order, AdminOrderStatusPayload $payload): void
    {
        $this->adminOrderValidationQuery->checkSameStatus($order, $payload);
        $this->adminOrderValidationQuery->checkRefundedStatus($order);
        $this->adminOrderValidationQuery->checkCompletedStatus($order, $payload);
    }
}
