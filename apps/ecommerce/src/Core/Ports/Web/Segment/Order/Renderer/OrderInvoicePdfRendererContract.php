<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Segment\Order\Renderer;

interface OrderInvoicePdfRendererContract
{
    /** @param array<string, mixed> $data */
    public function render(array $data): string;
}
