<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query;

use Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\Segment\Product\Entity\ProductSubtype;

use App\Core\Application\{
    Admin\Abstract\AbstractAdminApiListQueryHandler,
    Admin\Api\Product\Resource\AdminApiProductSubtypeResource
};

use App\Core\Ports\{
    Segment\Product\Repository\ProductSubtypeRepositoryContract,
    Shared\Logging\AppLoggerContract
};

final class AdminApiProductSubtypeListQueryHandler extends AbstractAdminApiListQueryHandler
{
    /**
     * @param ProductSubtypeRepositoryContract $productSubtypeRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ProductSubtypeRepositoryContract $productSubtypeRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param array{id?: int|null} $context
     *
     * @return ProductSubtype[]
     *
     * @throws \InvalidArgumentException
    */
    protected function getRepositoryClass(array $context = []): array
    {
        $productId = IdAssertion::assert(
            $context['id'] ?? null,
            'Product ID',
            \InvalidArgumentException::class,
        );

        return $this->productSubtypeRepository->findAllByProductId($productId);
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiProductSubtypeResource::class;
    }
}
