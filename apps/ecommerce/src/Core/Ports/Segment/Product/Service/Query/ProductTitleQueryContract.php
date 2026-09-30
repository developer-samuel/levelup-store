<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

interface ProductTitleQueryContract
{
    /**
     * @param string|null $category
     * @param string|null $type
     * @param bool $isDiscountRoute
     *
     * @return string
    */
    public function generateTitle(?string $category, ?string $type, bool $isDiscountRoute): string;
}
