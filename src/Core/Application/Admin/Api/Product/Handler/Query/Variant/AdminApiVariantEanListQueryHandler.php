<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantEan;

use App\Core\Application\{
    Admin\Api\Product\Handler\Query\Variant\Abstract\AbstractAdminApiVariantQueryHandler,
    Admin\Api\Product\Resource\Variant\AdminApiVariantEanResource
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract
};

class AdminApiVariantEanListQueryHandler extends AbstractAdminApiVariantQueryHandler
{
    /**
     * @param ProductVariantEanRepositoryContract $eanRepository
     * @param ProductVariantRepositoryContract $variantRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private ProductVariantEanRepositoryContract $eanRepository,
        ProductVariantRepositoryContract $variantRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $variantRepository,
            $logger,
        );
    }

    /**
     * @param int $variantId
     *
     * @return array<int, ProductVariantEan>
    */
    protected function getItemsForVariant(int $variantId): array
    {
        $variant = $this->findVariant($variantId);
        if (!$variant) {
            return [];
        }

        $eans = $this->eanRepository->findAvailableByVariant($variant);

        return array_values($eans);
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiVariantEanResource::class;
    }
}
