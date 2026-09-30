<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

use App\Core\Application\Segment\Product\Resource\ProductDescriptionResource;

use App\Core\Ports\Segment\Product\Service\Query\ProductDescriptionQueryContract;

final class ProductDescriptionQueryService implements ProductDescriptionQueryContract
{
    /**
     * @param ProductVariant $variant
     *
     * @return array<int, array<string, mixed>>
    */
    public function getProductDescriptions(ProductVariant $variant): array
    {
        $descriptions = $this->getDescriptionsFromVariant($variant);

        return $this->mapDescriptionsToResources($descriptions);
    }

    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariantDescription[]
    */
    private function getDescriptionsFromVariant(ProductVariant $variant): array
    {
        return $variant->getDescriptions()->toArray();
    }

    /**
     * @param ProductVariantDescription[] $descriptions
     *
     * @return array<int, array<string, mixed>>
    */
    private function mapDescriptionsToResources(array $descriptions): array
    {
        return array_values(array_map(
            static fn(ProductVariantDescription $description): array =>
                ProductDescriptionResource::toArray($description),
            $descriptions,
        ));
    }
}
