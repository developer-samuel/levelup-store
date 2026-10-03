<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Brand;

use Doctrine\Persistence\ManagerRegistry;

use Packages\Kit\Utils\Shared\StringNormalizer;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

use App\Core\Ports\Segment\Brand\BrandRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/** @extends AbstractRepository<Brand> */
final class BrandRepository extends AbstractRepository implements BrandRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Brand::class,
        );
    }

    protected function getAlias(): string
    {
        return 'b';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'name';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /** @return Brand[] */
    public function findAllWithProducts(?string $category = null, ?string $type = null): array
    {
        $brands = $this->findAll();

        return $this->filterBrandsByProductCriteria($brands, $category, $type);
    }

    public function findById(int $id): ?Brand
    {
        return $this->find($id);
    }

    public function findByName(string $name): ?Brand
    {
        return $this->findOneByColumn('name', $name);
    }

    public function existsByName(string $name): bool
    {
        return $this->existsByField('name', $name);
    }

    /**
     * @param Brand[] $brands
     *
     * @return Brand[]
    */
    private function filterBrandsByProductCriteria(array $brands, ?string $category, ?string $type): array
    {
        return array_filter($brands, function (Brand $brand) use ($category, $type) {
            return $this->hasMatchingProducts($brand, $category, $type);
        });
    }

    private function hasMatchingProducts(Brand $brand, ?string $category, ?string $type): bool
    {
        foreach ($brand->getProducts() as $product) {
            if (!$this->matchesCategory($product, $category)) {
                continue;
            }

            if (!$this->matchesType($product, $type)) {
                continue;
            }

            foreach ($product->getVariants() as $variant) {
                if ($this->hasStockAndEan($variant)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasStockAndEan(ProductVariant $variant): bool
    {
        if ($variant->getInStock() === null) {
            return false;
        }

        return $variant->getEans()->exists(function($key, $ean) {
            return $ean->getStatus() === ProductVariantEanStatus::ACTIVE;
        });
    }

    private function matchesCategory(Product $product, ?string $category): bool
    {
        $categoryName = $product->getCategory()->getName();

        return $this->matches($categoryName, $category);
    }

    private function matchesType(Product $product, ?string $type): bool
    {
        $typeName = $product->getType()->getName();

        return $this->matches($typeName, $type);
    }

    private function matches(?string $entityValue, ?string $filterValue): bool
    {
        if ($filterValue === null) {
            return true;
        }

        if ($entityValue === null) {
            return false;
        }

        return StringNormalizer::toLowerCase($entityValue) === StringNormalizer::toLowerCase($filterValue);
    }
}
