<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Traits;

use App\Core\Domain\Segment\Order\Entity\Order;

trait OrderTrait
{
    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }
}
