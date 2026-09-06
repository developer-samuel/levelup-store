<?php

declare(strict_types=1);

namespace Tests\Support\Factory;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Category\Entity\Category,
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Type\Entity\Type
};

trait ProductVariantFactory
{
    private function createAndPersistVariant(string $sku, string $name, string $url): ProductVariant
    {
        $product = $this->createAndPersistProduct();

        $variant = (new ProductVariant())
            ->setProduct($product)
            ->setSku($sku)
            ->setName($name)
            ->setUrl($url)
            ->setPrice(99.99);

        $this->em->persist($variant);
        $this->em->flush();

        return $variant;
    }

    private function createAndPersistProduct(): Product
    {
        $category = (new Category())->setName(substr(md5(uniqid('', true)), 0, 20));
        $type = (new Type())->setName('Type ' . uniqid('', true))->setCategory($category);
        $brand = (new Brand())->setName('Brand ' . uniqid('', true));

        $this->em->persist($category);
        $this->em->persist($type);
        $this->em->persist($brand);

        $product = (new Product())
            ->setName('Product ' . uniqid('', true))
            ->setCategory($category)
            ->setType($type)
            ->setBrand($brand)
            ->setCatalogCode('CAT-' . uniqid('', true));

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    private function createAndPersistEan(ProductVariant $variant, string $code): ProductVariantEan
    {
        $ean = (new ProductVariantEan())
            ->setVariant($variant)
            ->setCode($code);

        $this->em->persist($ean);
        $this->em->flush();

        return $ean;
    }
}
