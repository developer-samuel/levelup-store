<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Handler\Command;

use Packages\Kit\Assertion\Domain\Product\Variant\ProductVariantDescriptionAssertion;

use App\Core\Domain\{
    Admin\Product\Payload\AdminVariantDescriptionPayload,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

use App\Core\Application\Admin\Segment\Product\Handler\Command\Abstract\AbstractAdminVariantCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\AdminVariantDescriptionCommandContract,
    Admin\Segment\Product\Service\Command\AdminVariantValidationCommandContract,
    Security\SecurityPolicyContract,
    Segment\Product\Repository\Variant\ProductVariantDescriptionRepositoryContract,
    Shared\Logging\AppLoggerContract
};

final class AdminVariantDescriptionCommandHandler extends AbstractAdminVariantCommandHandler
{
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

    protected function getPayloadClass(): string
    {
        return AdminVariantDescriptionPayload::class;
    }

    protected function getEntityName(): string
    {
        return 'Description';
    }

    protected function getEntityOrFail(int $id): ProductVariantDescription
    {
        $entity = $this->variantDescriptionRepository->findById($id);
        ProductVariantDescriptionAssertion::assertExists($entity);

        return $entity;
    }

    protected function createEntity(int $variantId, object $payload): void
    {
        /** @var AdminVariantDescriptionPayload $payload */
        $this->adminVariantDescriptionCommand->createDescription($variantId, $payload);
    }

    protected function updateEntity(int $id, int $variantId, object $payload): void
    {
        /** @var AdminVariantDescriptionPayload $payload */
        $this->adminVariantDescriptionCommand->updateDescription($id, $variantId, $payload);
    }

    protected function destroyEntity(object $entity): void
    {
        /** @var ProductVariantDescription $entity */
        $this->adminVariantDescriptionCommand->destroyDescription($entity);
    }
}
