<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Segment\Order\Renderer;

interface OrderInvoicePdfRendererContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @return string
    */
    public function render(array $data): string;
}
