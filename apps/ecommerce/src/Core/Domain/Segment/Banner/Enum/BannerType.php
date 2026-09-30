<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Banner\Enum;

use App\Shared\{
    Traits\Enum\HasEnumLabel,
    Traits\Enum\HasEnumValue
};

enum BannerType: string
{
    use HasEnumLabel;
    use HasEnumValue;

    case BACKGROUND = 'background';
    case RECOMMENDED_PRODUCTS = 'recommended_products';
}
