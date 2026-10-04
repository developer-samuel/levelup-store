<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Segment\Order\Renderer\Email;

use App\Core\Domain\Segment\Order\Entity\Order;

interface OrderStatusEmailRendererContract
{
    public function renderOrderStatusEmail(Order $order, string $url): string;
}
