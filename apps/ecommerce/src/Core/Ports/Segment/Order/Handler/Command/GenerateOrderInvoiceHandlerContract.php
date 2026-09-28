<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Handler\Command;

interface GenerateOrderInvoiceHandlerContract
{
    /**
     * @param string $code
     *
     * @return string
    */
    public function handle(string $code): string;
}
