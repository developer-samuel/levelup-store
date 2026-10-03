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

final class AdminApiVariantEanListQueryHandler extends AbstractAdminApiVariantQueryHandler
{
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

    /** @return array<int, ProductVariantEan> */
    protected function getItemsForVariant(int $variantId): array
    {
        $variant = $this->findVariant($variantId);
        if ($variant === null) {
            return [];
        }

        $eans = $this->eanRepository->findAvailableByVariant($variant);

        return array_values($eans);
    }

    protected function getResourceClass(): string
    {
        return AdminApiVariantEanResource::class;
    }
}
