<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

interface ProductTitleQueryContract
{
    public function generateTitle(?string $category, ?string $type, bool $isDiscountRoute): string;
}
