<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Builder;

use App\Core\Ports\{
    Segment\Order\Service\Query\OrderCacheQueryContract,
    Segment\Order\Service\Query\OrderCountryQueryContract,
    Segment\Order\Service\Query\OrderItemQueryContract,
    Segment\Order\Service\Query\OrderPaymentQueryContract,
    Segment\Order\Service\Query\OrderPreparationQueryContract,
    Segment\Order\Service\Query\OrderPriceQueryContract,
    Segment\Order\Service\Query\OrderValidatorQueryContract
};

final readonly class OrderQueryBuilder
{
    public function __construct(
        public OrderCountryQueryContract $orderCountryQuery,
        public OrderPreparationQueryContract $orderPreparationQuery,
        public OrderItemQueryContract $orderItemQuery,
        public OrderPriceQueryContract $orderPriceQuery,
        public OrderPaymentQueryContract $orderPaymentQuery,
        public OrderValidatorQueryContract $orderValidatorQuery,
        public OrderCacheQueryContract $orderCacheQuery,
    ) {}
}
