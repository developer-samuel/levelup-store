<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

interface ProductCacheQueryContract
{
    public function getTitle(
        ?string $category,
        ?string $type,
        bool $isDiscount,
    ): string;

    public function getRoute(string $path): string;
}
