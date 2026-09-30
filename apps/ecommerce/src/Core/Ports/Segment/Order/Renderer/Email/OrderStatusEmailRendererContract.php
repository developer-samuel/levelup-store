<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Renderer\Email;

use App\Core\Domain\Segment\Order\Entity\Order;

interface OrderStatusEmailRendererContract
{
    /**
     * @param Order $order
     * @param string $url
     *
     * @return string
    */
    public function renderOrderStatusEmail(Order $order, string $url): string;
}
