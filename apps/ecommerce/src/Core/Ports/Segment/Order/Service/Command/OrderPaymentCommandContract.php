<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\Segment\Order\Entity\Order;

interface OrderPaymentCommandContract
{
    public function processSuccess(string $sessionId): Order;
}
