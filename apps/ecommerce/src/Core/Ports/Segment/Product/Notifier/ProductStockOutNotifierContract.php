<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Notifier;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantStock;

interface ProductStockOutNotifierContract
{
    public function notify(ProductVariantStock $stock): void;
}
