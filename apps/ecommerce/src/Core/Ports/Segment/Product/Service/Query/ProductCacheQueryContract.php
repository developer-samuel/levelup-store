<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

interface ProductCacheQueryContract
{
    /**
     * @param string|null $category
     * @param string|null $type
     * @param bool $isDiscount
     *
     * @return string
    */
    public function getTitle(
        ?string $category,
        ?string $type,
        bool $isDiscount,
    ): string;

    /**
     * @param string $path
     *
     * @return string
    */
    public function getRoute(string $path): string;
}
