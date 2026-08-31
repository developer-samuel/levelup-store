<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query\Variant\Abstract;

use Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

use App\Core\Application\Admin\Abstract\AbstractAdminApiListQueryHandler;

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract
};

abstract class AbstractAdminApiVariantQueryHandler extends AbstractAdminApiListQueryHandler
{
    /**
     * @param ProductVariantRepositoryContract $variantRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        protected readonly ProductVariantRepositoryContract $variantRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param int $variantId
     *
     * @return array<int, object>
    */
    abstract protected function getItemsForVariant(int $variantId): array;

    /**
     * @param int $variantId
     *
     * @return ProductVariant|null
    */
    protected function findVariant(int $variantId): ?ProductVariant
    {
        $variant = $this->variantRepository->findById($variantId);

        return $variant instanceof ProductVariant ? $variant : null;
    }

    /**
     * @param array{variantId?: int|null} $context
     *
     * @return array<int, ProductVariant>
     *
     * @throws \InvalidArgumentException
    */
    protected function getRepositoryClass(array $context = []): array
    {
        $variantId = IdAssertion::assert(
            $context['variantId'] ?? null,
            'Variant ID',
            \InvalidArgumentException::class,
        );

        /** @var array<int, ProductVariant> $items */
        $items = $this->getItemsForVariant($variantId);

        return $items;
    }
}
