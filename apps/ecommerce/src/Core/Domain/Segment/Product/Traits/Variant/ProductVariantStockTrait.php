<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits\Variant;

use App\Core\Domain\Segment\Product\Enum\ProductStockStatus;

trait ProductVariantStockTrait
{
    public function reserveQuantity(int $quantity): void
    {
        $this->quantityReserved += $quantity;

        $remaining = $this->quantityAvailable - $quantity;
        $this->quantityAvailable = max($remaining, 0);

        if ($remaining <= 0) {
            $this->status = ProductStockStatus::OUT_OF_STOCK;
        }
    }

    public function markCompleted(): void
    {
        $this->quantityReserved = max($this->quantityReserved - 1, 0);
    }

    public function markRefunded(): void
    {
        $this->quantityRefunded++;
    }

    public function recalculateStatus(): void
    {
        $this->status = $this->quantityAvailable <= 0
            ? ProductStockStatus::OUT_OF_STOCK
            : ProductStockStatus::IN_STOCK;
    }
}
