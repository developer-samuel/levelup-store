<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query\Variant\Abstract;

use Packages\Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

use App\Core\Application\Admin\Abstract\AbstractAdminApiListQueryHandler;

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract
};

abstract class AbstractAdminApiVariantQueryHandler extends AbstractAdminApiListQueryHandler
{
    public function __construct(
        protected readonly ProductVariantRepositoryContract $variantRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /** @return array<int, object> */
    abstract protected function getItemsForVariant(int $variantId): array;

    protected function findVariant(int $variantId): ?ProductVariant
    {
        $variant = $this->variantRepository->findById($variantId);

        return $variant instanceof ProductVariant ? $variant : null;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<int, ProductVariant>
    */
    protected function getRepositoryClass(array $context = []): array
    {
        /** @var array{variantId?: int|null} $context */
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
