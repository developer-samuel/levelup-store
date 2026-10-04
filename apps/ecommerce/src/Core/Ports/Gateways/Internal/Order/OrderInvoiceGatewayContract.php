<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\Internal\Order;

interface OrderInvoiceGatewayContract
{
    /** @param array<string, mixed> $data */
    public function generate(array $data): string;
}
