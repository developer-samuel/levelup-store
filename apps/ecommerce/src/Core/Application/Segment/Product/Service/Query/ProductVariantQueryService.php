<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductVariantObject
};

use App\Core\Ports\{
    Segment\Product\Assembler\ProductVariantAssemblerContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Product\Service\Query\ProductVariantQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

final readonly class ProductVariantQueryService implements ProductVariantQueryContract
{
    /**
     * @param ReviewQueryContract $reviewQuery
     * @param ProductVariantRepositoryContract $variantRepository
     * @param ProductVariantAssemblerContract $productVariantAssembler
    */
    public function __construct(
        private ReviewQueryContract $reviewQuery,
        private ProductVariantRepositoryContract $variantRepository,
        private ProductVariantAssemblerContract $productVariantAssembler,
    ) {}

    /**
     * @param ProductVariant[] $variants
     *
     * @return ProductVariantObject[]
    */
    public function mapVariantsToData(array $variants): array
    {
        return array_map(
            fn(ProductVariant $v): ProductVariantObject =>
                $this->productVariantAssembler->toObject($v, $this->reviewQuery),
            $variants,
        );
    }

    /**
     * @param string $url
     *
     * @return ProductVariant|null
    */
    public function getVariantOrNull(string $url): ?ProductVariant
    {
        return $this->variantRepository->findOneByUrl($url);
    }

    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariant[]
    */
    public function getAllVariantsOrNull(ProductVariant $variant): array
    {
        return array_values(
            $this->variantRepository->findAllByProduct($variant->getProduct()),
        );
    }
}
