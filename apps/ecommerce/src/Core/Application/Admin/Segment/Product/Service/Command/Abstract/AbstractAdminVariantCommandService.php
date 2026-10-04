<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Service\Command\Abstract;

use Packages\Kit\Assertion\Domain\Product\Variant\ProductVariantAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

abstract class AbstractAdminVariantCommandService
{
    public function __construct(
        protected readonly EntityPersistenceContract $entityPersistence,
        protected readonly ProductVariantRepositoryContract $variantRepository,
    ) {}

    protected function resolveVariant(int $variantId): ProductVariant
    {
        $variant = $this->variantRepository->findById($variantId);
        ProductVariantAssertion::assertExists($variant);

        return $variant;
    }

    protected function saveEntityWithVariant(object $entity, ProductVariant $variant): object
    {
        // @phpstan-ignore-next-line
        $entity->setVariant($variant);

        $this->entityPersistence->persist($entity, true);

        return $entity;
    }
}
