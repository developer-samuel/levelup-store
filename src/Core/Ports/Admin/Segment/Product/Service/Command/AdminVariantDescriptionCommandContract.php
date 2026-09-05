<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Product\Service\Command;

use App\Core\Domain\{
    Admin\Product\Payload\AdminVariantDescriptionPayload,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

interface AdminVariantDescriptionCommandContract
{
    /**
     * @param int $variantId
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return ProductVariantDescription
    */
    public function createDescription(int $variantId, AdminVariantDescriptionPayload $payload): ProductVariantDescription;

    /**
     * @param int $descriptionId
     * @param int $variantId
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return ProductVariantDescription
    */
    public function updateDescription(int $descriptionId, int $variantId, AdminVariantDescriptionPayload $payload): ProductVariantDescription;

    /**
     * @param ProductVariantDescription $description
     *
     * @return void
    */
    public function destroyDescription(ProductVariantDescription $description): void;
}
