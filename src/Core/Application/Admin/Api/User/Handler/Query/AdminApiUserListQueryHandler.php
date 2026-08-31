<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\User\Handler\Query;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Application\{
    Admin\Abstract\AbstractAdminApiListQueryHandler,
    Admin\Api\User\Resource\AdminApiUserResource
};

use App\Core\Ports\{
    Segment\User\Repository\UserRepositoryContract,
    Shared\Logging\AppLoggerContract
};

class AdminApiUserListQueryHandler extends AbstractAdminApiListQueryHandler
{
    /**
     * @param UserRepositoryContract $userRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly UserRepositoryContract $userRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @return User[]
    */
    protected function getRepositoryClass(array $context = []): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiUserResource::class;
    }
}
