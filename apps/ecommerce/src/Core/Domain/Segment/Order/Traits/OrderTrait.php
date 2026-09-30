<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Traits;

use App\Core\Domain\Segment\Order\Entity\Order;

/**
 * @property Order $order
*/
trait OrderTrait
{
    /**
     * @return Order
    */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * 
     * @return self
    */
    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }
}
