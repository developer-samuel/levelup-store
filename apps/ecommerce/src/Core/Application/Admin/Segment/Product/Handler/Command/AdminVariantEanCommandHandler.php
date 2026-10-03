<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Handler\Command;

use Packages\Kit\Assertion\Domain\Product\Variant\ProductVariantEanAssertion;

use App\Core\Domain\{
    Admin\Product\Payload\AdminVariantEanPayload,
    Segment\Product\Entity\Variant\ProductVariantEan
};

use App\Core\Application\Admin\Segment\Product\Handler\Command\Abstract\AbstractAdminVariantCommandHandler;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\AdminVariantEanCommandContract,
    Admin\Segment\Product\Service\Command\AdminVariantValidationCommandContract,
    Security\SecurityPolicyContract,
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Shared\Logging\AppLoggerContract
};

final class AdminVariantEanCommandHandler extends AbstractAdminVariantCommandHandler
{
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

    protected function getPayloadClass(): string
    {
        return AdminVariantEanPayload::class;
    }

    protected function getEntityName(): string
    {
        return 'EAN';
    }

    protected function getEntityOrFail(int $id): ProductVariantEan
    {
        $ean = $this->repository->findById($id);
        ProductVariantEanAssertion::assertExists($ean);

        return $ean;
    }

    protected function createEntity(int $variantId, object $payload): void
    {
        /** @var AdminVariantEanPayload $payload */
        $this->adminCommand->createEan($variantId, $payload);
    }

    protected function updateEntity(int $id, int $variantId, object $payload): void
    {
        /** @var AdminVariantEanPayload $payload */
        $this->adminCommand->updateEan($id, $variantId, $payload);
    }

    protected function destroyEntity(object $entity): void
    {
        /** @var ProductVariantEan $entity */
        $this->adminCommand->destroyEan($entity);
    }
}
