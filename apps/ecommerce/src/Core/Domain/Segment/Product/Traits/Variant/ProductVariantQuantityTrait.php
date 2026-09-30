<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits\Variant;

use Doctrine\ORM\Mapping as ORM;

trait ProductVariantQuantityTrait
{
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $quantityAvailable = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $quantityReserved = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $quantityRefunded = 0;

    /**
     * @return int
    */
    public function getQuantityAvailable(): int
    {
        return $this->quantityAvailable;
    }

    /**
     * @param int $quantityAvailable
     *
     * @return self
    */
    public function setQuantityAvailable(int $quantityAvailable): self
    {
        $this->quantityAvailable = $quantityAvailable;
        return $this;
    }

    /**
     * @return int
    */
    public function getQuantityReserved(): int
    {
        return $this->quantityReserved;
    }

    /**
     * @param int $quantityReserved
     *
     * @return self
    */
    public function setQuantityReserved(int $quantityReserved): self
    {
        $this->quantityReserved = $quantityReserved;
        return $this;
    }

    /**
     * @return int
    */
    public function getQuantityRefunded(): int
    {
        return $this->quantityRefunded;
    }

    /**
     * @param int $quantityRefunded
     *
     * @return self
    */
    public function setQuantityRefunded(int $quantityRefunded): self
    {
        $this->quantityRefunded = $quantityRefunded;
        return $this;
    }
}
