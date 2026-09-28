<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product;

use Kit\Utils\Product\ProductCatalogCodeGenerator;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Category\Entity\Category,
    Segment\Product\Entity\Product,
    Segment\Type\Entity\Type
};

trait ProductFactory
{
    /**
     * @param string $productName
     * @param Category $category
     * @param Type $type
     * @param Brand $brand
     *
     * @return Product
    */
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
