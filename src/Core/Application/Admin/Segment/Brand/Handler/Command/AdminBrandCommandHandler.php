<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Brand\Handler\Command;

use App\Core\Domain\Admin\Segment\Brand\Payload\AdminBrandPayload;

use App\Core\Application\Admin\Abstract\AbstractAdminFormCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Brand\Service\Command\AdminBrandCommandContract,
    Security\Policy\SecurityPolicyContract,
    Segment\Brand\Service\Query\BrandQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

class AdminBrandCommandHandler extends AbstractAdminFormCommandHandler
{
    /**
     * @param BrandQueryContract $brandQuery
     * @param AdminBrandCommandContract $adminBrandCommand
     * @param SecurityPolicyContract $securityPolicy
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly BrandQueryContract $brandQuery,
        private readonly AdminBrandCommandContract $adminBrandCommand,
        SecurityPolicyContract $securityPolicy,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityPolicy,
            $logger,
        );
    }

    /**
     * @param AdminBrandPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handleCreate(AdminBrandPayload $payload): array
    {
        return $this->executeAdmin(function() use ($payload) {
            $this->adminBrandCommand->createBrand($payload);

            return ApiResultFormatter::success('Brand created successfully.');
        });
    }

    /**
     * @param AdminBrandPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handleUpdate(AdminBrandPayload $payload): array
    {
        return $this->execute(function() use ($payload) {
            $id = $this->adminBrandCommand->validateId($payload);

            $this->adminBrandCommand->updateBrand($id, $payload);

            return ApiResultFormatter::success('Brand updated successfully.');
        });
    }

    /**
     * @param int $brandId
     *
     * @return array<string, mixed>
    */
    public function handleDestroy(int $brandId): array
    {
        return $this->execute(function() use ($brandId) {
            $brand = $this->brandQuery->getBrandByIdOrFail($brandId);

            $this->adminBrandCommand->destroyBrand($brand);

            return ApiResultFormatter::success('Brand deleted successfully.');
        });
    }
}
