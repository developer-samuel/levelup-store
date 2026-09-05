<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\Internal\Order;

interface OrderInvoiceGatewayContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @return string
    */
    public function generate(array $data): string;
}
