<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product;

use Packages\Kit\Utils\Product\ProductCatalogCodeGenerator;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Category\Entity\Category,
    Segment\Product\Entity\Product,
    Segment\Type\Entity\Type
};

trait ProductFactory
{
    private function createProduct(
        string $productName,
        Category $category,
        Type $type,
        Brand $brand,
    ): Product {
        return (new Product())
            ->setCategory($category)
            ->setType($type)
            ->setBrand($brand)
            ->setCatalogCode(
                ProductCatalogCodeGenerator::generateCatalogCode($productName),
            )
            ->setName($productName);
    }
}
