<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Handler\Command\Variant;

use Kit\Assertion\Domain\Product\Variant\ProductVariantDescriptionAssertion;

use App\Core\Domain\{
    Admin\Segment\Product\Payload\Variant\AdminVariantDescriptionPayload,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

use App\Core\Application\Admin\Segment\Product\Handler\Command\Variant\Abstract\AbstractAdminVariantCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\Variant\AdminVariantDescriptionCommandContract,
    Admin\Segment\Product\Service\Command\Variant\AdminVariantValidationCommandContract,
    Security\Policy\SecurityPolicyContract,
    Segment\Product\Repository\Variant\ProductVariantDescriptionRepositoryContract,
    Shared\Logging\AppLoggerContract
};

class AdminVariantDescriptionCommandHandler extends AbstractAdminVariantCommandHandler
{
    /**
     * @param ProductVariantDescriptionRepositoryContract $variantDescriptionRepository
     * @param AdminVariantDescriptionCommandContract $adminVariantDescriptionCommand
     * @param AdminVariantValidationCommandContract $adminVariantValidationCommand
     * @param SecurityPolicyContract $securityPolicy
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ProductVariantDescriptionRepositoryContract $variantDescriptionRepository,
        private readonly AdminVariantDescriptionCommandContract $adminVariantDescriptionCommand,
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
        return AdminVariantDescriptionPayload::class;
    }

    /**
     * @return string
    */
    protected function getEntityName(): string
    {
        return 'Description';
    }

    /**
     * @param int $id
     *
     * @return ProductVariantDescription
    */
    protected function getEntityOrFail(int $id): ProductVariantDescription
    {
        $entity = $this->variantDescriptionRepository->findById($id);
        ProductVariantDescriptionAssertion::assertExists($entity);

        return $entity;
    }

    /**
     * @param int $variantId
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return void
    */
    protected function createEntity(int $variantId, object $payload): void
    {
        $this->adminVariantDescriptionCommand->createDescription($variantId, $payload);
    }

    /**
     * @param int $id
     * @param int $variantId
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return void
    */
    protected function updateEntity(int $id, int $variantId, object $payload): void
    {
        $this->adminVariantDescriptionCommand->updateDescription($id, $variantId, $payload);
    }

    /**
     * @param ProductVariantDescription $entity
     *
     * @return void
    */
    protected function destroyEntity(object $entity): void
    {
        $this->adminVariantDescriptionCommand->destroyDescription($entity);
    }
}
