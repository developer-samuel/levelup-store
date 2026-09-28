<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Product\Service\Command;

use App\Core\Domain\{
    Admin\Product\Payload\AdminVariantEanPayload,
    Segment\Product\Entity\Variant\ProductVariantEan
};

interface AdminVariantEanCommandContract
{
    /**
     * @param int $variantId
     * @param AdminVariantEanPayload $payload
     *
     * @return ProductVariantEan
    */
    public function createEan(int $variantId, AdminVariantEanPayload $payload): ProductVariantEan;

    /**
     * @param int $eanId
     * @param int $variantId
     * @param AdminVariantEanPayload $payload
     *
     * @return ProductVariantEan
    */
    public function updateEan(int $eanId, int $variantId, AdminVariantEanPayload $payload): ProductVariantEan;

    /**
     * @param ProductVariantEan $ean
     *
     * @return void
    */
    public function destroyEan(ProductVariantEan $ean): void;
}
