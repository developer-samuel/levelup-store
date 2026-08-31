<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantImage;

use App\Core\Application\{
    Admin\Api\Product\Handler\Query\Variant\Abstract\AbstractAdminApiVariantQueryHandler,
    Admin\Api\Product\Resource\Variant\AdminApiVariantImageResource
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantImageRepositoryContract,
    Shared\Logging\AppLoggerContract
};

class AdminApiVariantImageListQueryHandler extends AbstractAdminApiVariantQueryHandler
{
    /**
     * @param ProductVariantImageRepositoryContract $imageRepository
     * @param ProductVariantRepositoryContract $variantRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private ProductVariantImageRepositoryContract $imageRepository,
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
     * @return array<int, ProductVariantImage>
    */
    protected function getItemsForVariant(int $variantId): array
    {
        $variant = $this->findVariant($variantId);
        if (!$variant) {
            return [];
        }

        $images = $this->imageRepository->findAllByVariant($variant);

        return array_values($images);
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiVariantImageResource::class;
    }
}
