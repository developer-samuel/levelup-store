<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Handler\Query;

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Segment\Order\Enum\OrderStatus,
    Segment\Order\Entity\Order,
    Segment\Order\Utils\OrderStatusResolver,
    Segment\Order\ValueObject\OrderDetailObject,
    Segment\Order\ValueObject\OrderItemObject,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Mapper\OrderAddressMapper,
    Segment\Order\Mapper\OrderPersonalMapper
};

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Order\Handler\Query\GetOrderDetailQueryHandlerContract,
    Segment\Order\Service\Query\OrderDetailQueryContract
};

/**
 * @phpstan-import-type ItemsWithTotal from OrderDetailQueryContract
*/
final readonly class GetOrderDetailQueryHandler implements GetOrderDetailQueryHandlerContract
{
    public function __construct(
        private SecurityPolicyContract $securityPolicy,
        private OrderDetailQueryContract $orderDetailQuery,
        private bool $wkhtmltopdfEnabled = false,
    ) {}

    public function handle(string $code, ?User $user = null): ?OrderDetailObject
    {
        $order = $this->fetchOrderOrNull($code);
        if ($order === null) {
            return null;
        }

        $this->authorizeUser($order, $user);

        ['items' => $items, 'total' => $totalPrice] = $this->buildItemsAndTotal($order);

        $statuses = $this->resolveStatuses($order);

        return $this->createOrderDetailResult($order, $items, $totalPrice, $statuses);
    }

    private function fetchOrderOrNull(string $code): ?Order
    {
        return $this->orderDetailQuery->fetchOrder($code);
    }

    private function authorizeUser(Order $order, ?User $user): void
    {
        if ($user !== null) {
            $this->securityPolicy->checkIfEmailVerified();
            if (!$order->isOwnedBy($user)) {
                throw new AccessDeniedException('User does not own this order.');
            }

            return;
        }

        $this->securityPolicy->checkAdminAccess();
    }

    /** @return ItemsWithTotal */
    private function buildItemsAndTotal(Order $order): array
    {
        return $this->orderDetailQuery->buildItemsWithTotal($order);
    }

    /** @return OrderStatus[] */
    private function resolveStatuses(Order $order): array
    {
        return OrderStatusResolver::resolveAvailableStatuses($order->getStatus());
    }

    /**
     * @param OrderItemObject[] $items
     * @param OrderStatus[] $statuses
    */
    private function createOrderDetailResult(
        Order $order,
        array $items,
        float $totalPrice,
        array $statuses,
    ): OrderDetailObject {
        return new OrderDetailObject(
            order: $order,
            totalPrice: $totalPrice,
            statuses: $statuses,
            items: $items,
            personal: OrderPersonalMapper::mapToCamelCase($order),
            billing: OrderAddressMapper::mapBillingCamelCase($order),
            shipping: OrderAddressMapper::mapShippingCamelCase($order),
            pdfEnabled: $this->wkhtmltopdfEnabled,
        );
    }
}
