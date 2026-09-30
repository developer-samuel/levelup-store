<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Resource;

use App\Core\Domain\Segment\Product\Entity\Product;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     name: string,
 *     catalogCode: string,
 *     category: string,
 *     type: string,
 *     brand: string,
 *     createdAt: string
 * }
*/
final class AdminApiProductResource
{
    /**
     * @param Product $product
     *
     * @return ResourceArray
    */
    public static function toArray(Product $product): array
    {
        return [
            'id'          => $product->getId(),
            'name'        => $product->getName(),
            'catalogCode' => $product->getCatalogCode(),
            'category'    => $product->getCategory()->getName(),
            'type'        => $product->getType()->getName(),
            'brand'       => $product->getBrand()->getName(),
            'createdAt'   => DateTimeFormatter::formatDMY($product->getCreatedAt()),
        ];
    }
}
