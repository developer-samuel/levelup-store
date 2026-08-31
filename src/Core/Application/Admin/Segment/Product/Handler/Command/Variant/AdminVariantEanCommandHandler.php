<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Handler\Command\Variant;

use Kit\Assertion\Domain\Product\Variant\ProductVariantEanAssertion;

use App\Core\Domain\{
    Admin\Segment\Product\Payload\Variant\AdminVariantEanPayload,
    Segment\Product\Entity\Variant\ProductVariantEan
};

use App\Core\Application\Admin\Segment\Product\Handler\Command\Variant\Abstract\AbstractAdminVariantCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\Variant\AdminVariantEanCommandContract,
    Admin\Segment\Product\Service\Command\Variant\AdminVariantValidationCommandContract,
    Security\Policy\SecurityPolicyContract,
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Shared\Logging\AppLoggerContract
};

class AdminVariantEanCommandHandler extends AbstractAdminVariantCommandHandler
{
    /**
     * @param ProductVariantEanRepositoryContract $repository
     * @param AdminVariantEanCommandContract $adminCommand
     * @param AdminVariantValidationCommandContract $adminVariantValidationCommand
     * @param SecurityPolicyContract $securityPolicy
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ProductVariantEanRepositoryContract $repository,
        private readonly AdminVariantEanCommandContract $adminCommand,
        AdminVariantValidationCommandContract $adminVariantValidationCommand,
        SecurityPolicyContract $securityPolicy,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $adminVariantValidationCommand,
            $securityPolicy,
            $logger,
        );
    }

    /**
     * @return string
    */
    protected function getPayloadClass(): string
    {
        return AdminVariantEanPayload::class;
    }

    /**
     * @return string
    */
    protected function getEntityName(): string
    {
        return 'EAN';
    }

    /**
     * @param int $id
     *
     * @return ProductVariantEan
    */
    protected function getEntityOrFail(int $id): ProductVariantEan
    {
        $ean = $this->repository->findById($id);
        ProductVariantEanAssertion::assertExists($ean);

        return $ean;
    }

    /**
     * @param int $variantId
     * @param AdminVariantEanPayload $payload
     *
     * @return void
    */
    protected function createEntity(int $variantId, object $payload): void
    {
        $this->adminCommand->createEan($variantId, $payload);
    }

    /**
     * @param int $id
     * @param int $variantId
     * @param AdminVariantEanPayload $payload
     *
     * @return void
    */
    protected function updateEntity(int $id, int $variantId, object $payload): void
    {
        $this->adminCommand->updateEan($id, $variantId, $payload);
    }

    /**
     * @param ProductVariantEan $entity
     *
     * @return void
    */
    protected function destroyEntity(object $entity): void
    {
        $this->adminCommand->destroyEan($entity);
    }
}
