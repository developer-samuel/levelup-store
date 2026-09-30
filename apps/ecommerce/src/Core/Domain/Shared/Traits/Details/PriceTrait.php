<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

/**
 * @property float $price
*/
trait PriceTrait
{
    /**
     * @return float
    */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return self
    */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
}
