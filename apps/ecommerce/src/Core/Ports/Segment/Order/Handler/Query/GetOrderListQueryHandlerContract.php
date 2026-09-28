<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Handler\Query;

use App\Core\Domain\Segment\Order\Entity\Order;

interface GetOrderListQueryHandlerContract
{
    /**
     * @return Order[]
    */
    public function handle(): array;
}
