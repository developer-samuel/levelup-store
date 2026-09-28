<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Handler\Query;

use App\Core\Domain\Segment\Product\Entity\Product;

use App\Core\Application\{
    Admin\Abstract\AbstractAdminApiListQueryHandler,
    Admin\Api\Product\Resource\AdminApiProductResource
};

use App\Core\Ports\{
    Segment\Product\Repository\ProductRepositoryContract,
    Shared\Logging\AppLoggerContract
};

final class AdminApiProductListQueryHandler extends AbstractAdminApiListQueryHandler
{
    /**
     * @param ProductRepositoryContract $productRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ProductRepositoryContract $productRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return Product[]
    */
    protected function getRepositoryClass(array $context = []): array
    {
        return $this->productRepository->findAll();
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiProductResource::class;
    }
}
