<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\Traits\Details;

trait PriceTrait
{
    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
}
