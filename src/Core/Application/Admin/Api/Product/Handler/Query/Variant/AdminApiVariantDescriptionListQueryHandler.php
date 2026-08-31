<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantDescription;

use App\Core\Application\{
    Admin\Api\Product\Handler\Query\Variant\Abstract\AbstractAdminApiVariantQueryHandler,
    Admin\Api\Product\Resource\Variant\AdminApiVariantDescriptionResource
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantDescriptionRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract
};

class AdminApiVariantDescriptionListQueryHandler extends AbstractAdminApiVariantQueryHandler
{
    /**
     * @param ProductVariantDescriptionRepositoryContract $descriptionRepository
     * @param ProductVariantRepositoryContract $variantRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private ProductVariantDescriptionRepositoryContract $descriptionRepository,
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
     * @return array<int, ProductVariantDescription>
    */
    protected function getItemsForVariant(int $variantId): array
    {
        $variant = $this->findVariant($variantId);
        if (!$variant) {
            return [];
        }

        $descriptions = $this->descriptionRepository->findAllByVariant($variant);

        return array_values($descriptions);
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiVariantDescriptionResource::class;
    }
}
