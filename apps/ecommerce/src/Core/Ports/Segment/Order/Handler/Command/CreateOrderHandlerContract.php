<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Handler\Command;

use App\Core\Domain\Segment\Order\Payload\OrderCreatePayload;

interface CreateOrderHandlerContract
{
    /**
     * @param OrderCreatePayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(OrderCreatePayload $payload): array;
}
