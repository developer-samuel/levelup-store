<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

interface OrderInvoiceQueryContract
{
    /** @return array<string, mixed> */
    public function getInvoiceDetails(string $code): array;
}
