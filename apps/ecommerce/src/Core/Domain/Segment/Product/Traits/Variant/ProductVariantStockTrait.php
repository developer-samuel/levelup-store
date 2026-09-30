<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits\Variant;

use App\Core\Domain\Segment\Product\Enum\ProductStockStatus;

/**
 * @property int $quantityAvailable
 * @property int $quantityReserved
 * @property int $quantityRefunded
 * @property ProductStockStatus $status
*/
trait ProductVariantStockTrait
{
    /**
     * @param int $quantity
     *
     * @return void
    */
    public function reserveQuantity(int $quantity): void
    {
        $this->quantityReserved += $quantity;

        $remaining = $this->quantityAvailable - $quantity;
        $this->quantityAvailable = max($remaining, 0);

        if ($remaining <= 0) {
            $this->status = ProductStockStatus::OUT_OF_STOCK;
        }
    }

    /**
     * @return void
    */
    public function markCompleted(): void
    {
        $this->quantityReserved = max($this->quantityReserved - 1, 0);
    }

    /**
     * @return void
    */
    public function markRefunded(): void
    {
        $this->quantityRefunded++;
    }

    /**
     * @return void
    */
    public function recalculateStatus(): void
    {
        $this->status = $this->quantityAvailable <= 0
            ? ProductStockStatus::OUT_OF_STOCK
            : ProductStockStatus::IN_STOCK;
    }
}
