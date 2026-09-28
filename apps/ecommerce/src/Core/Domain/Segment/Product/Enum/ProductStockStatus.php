<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Enum;

use App\Shared\Traits\Enum\HasEnumLabel;

enum ProductStockStatus: string
{
    use HasEnumLabel;

    case IN_STOCK = 'in_stock';
    case OUT_OF_STOCK = 'out_of_stock';
}
