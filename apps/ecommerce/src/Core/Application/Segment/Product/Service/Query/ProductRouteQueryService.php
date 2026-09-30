<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use App\Core\Application\Segment\Product\Utils\PathChecker;

use App\Core\Ports\Segment\Product\Service\Query\ProductRouteQueryContract;

final class ProductRouteQueryService implements ProductRouteQueryContract
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function generateRoute(string $path): string
    {
        return match (true) {
            $this->isProductPath($path)        => 'products_index',
            PathChecker::isDiscountPath($path) => 'discounts',
            default                            => '',
        };
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function isProductPath(string $path): bool
    {
        return str_starts_with($path, '/products') && $path !== '/product/show';
    }
}
