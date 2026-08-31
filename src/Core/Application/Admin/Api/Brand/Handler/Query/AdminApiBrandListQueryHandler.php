<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Brand\Handler\Query;

use App\Core\Domain\Segment\Brand\Entity\Brand;

use App\Core\Application\{
    Admin\Abstract\AbstractAdminApiListQueryHandler,
    Admin\Api\Brand\Resource\AdminApiBrandResource
};

use App\Core\Ports\{
    Segment\Brand\Repository\BrandRepositoryContract,
    Shared\Logging\AppLoggerContract
};

class AdminApiBrandListQueryHandler extends AbstractAdminApiListQueryHandler
{
    /**
     * @param BrandRepositoryContract $brandRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly BrandRepositoryContract $brandRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @return Brand[]
    */
    protected function getRepositoryClass(array $context = []): array
    {
        return $this->brandRepository->findAll();
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiBrandResource::class;
    }
}
