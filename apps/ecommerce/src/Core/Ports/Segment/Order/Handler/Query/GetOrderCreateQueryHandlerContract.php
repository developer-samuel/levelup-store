<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Handler\Query;

use App\Core\Domain\Segment\Order\ValueObject\OrderCreateObject;

interface GetOrderCreateQueryHandlerContract
{
    /**
     * @return OrderCreateObject
    */
    public function handle(): OrderCreateObject;
}
