<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Service\Command;

use Kit\{
    Assertion\Domain\Product\Variant\ProductVariantDescriptionAssertion,
    Assertion\Shared\EntityAssertion
};

use App\Core\Domain\{
    Admin\Product\Payload\AdminVariantDescriptionPayload,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

use App\Core\Application\Admin\Segment\Product\Service\Command\Abstract\AbstractAdminVariantCommandService;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\AdminVariantDescriptionCommandContract,
    Segment\Product\Repository\Variant\ProductVariantDescriptionRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

final class AdminVariantDescriptionCommandService extends AbstractAdminVariantCommandService implements AdminVariantDescriptionCommandContract
{
    /**
     * @param ProductVariantDescriptionRepositoryContract $variantDescriptionRepository
     * @param EntityPersistenceContract $entityPersistence
     * @param ProductVariantRepositoryContract $variantRepository
    */
    public function __construct(
        private readonly ProductVariantDescriptionRepositoryContract $variantDescriptionRepository,
        EntityPersistenceContract $entityPersistence,
        ProductVariantRepositoryContract $variantRepository,
    ) {
        parent::__construct(
            $entityPersistence,
            $variantRepository,
        );
    }

    /**
     * @param int $variantId
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return ProductVariantDescription
    */
    public function createDescription(int $variantId, AdminVariantDescriptionPayload $payload): ProductVariantDescription
    {
        $variant = $this->resolveVariant($variantId);

        $position = $this->getNextDescriptionPosition($variantId);

        $description = $this->buildDescriptionEntity($variant, $position, $payload);

        $this->saveEntityWithVariant($description, $variant);

        return $description;
    }

    /**
     * @param int $descriptionId
     * @param int $variantId
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return ProductVariantDescription
    */
    public function updateDescription(int $descriptionId, int $variantId, AdminVariantDescriptionPayload $payload): ProductVariantDescription
    {
        $this->resolveVariant($variantId);

        $description = $this->getDescription($descriptionId);

        $this->updateDescriptionEntity($description, $payload);

        $this->entityPersistence->persist($description, true);

        return $description;
    }

    /**
     * @param ProductVariantDescription $description
     *
     * @return void
    */
    public function destroyDescription(ProductVariantDescription $description): void
    {
        $this->entityPersistence->remove($description, true);
    }

    /**
     * @param int $variantId
     *
     * @return int
    */
    private function getNextDescriptionPosition(int $variantId): int
    {
        return $this->variantDescriptionRepository->getMaxPositionByVariantId($variantId) + 1;
    }

    /**
     * @param ProductVariantDescription $description
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return void
    */
    private function updateDescriptionEntity(ProductVariantDescription $description, AdminVariantDescriptionPayload $payload): void
    {
        $description->setTitle($payload->title)
            ->setBody($payload->body)
            ->setUpdatedAt();
    }

    /**
     * @param ProductVariant $variant
     * @param int $position
     * @param AdminVariantDescriptionPayload $payload
     *
     * @return ProductVariantDescription
    */
    private function buildDescriptionEntity(
        ProductVariant $variant,
        int $position,
        AdminVariantDescriptionPayload $payload,
    ): ProductVariantDescription {
        return (new ProductVariantDescription())
            ->setVariant($variant)
            ->setPosition($position)
            ->setTitle($payload->title)
            ->setBody($payload->body);
    }

    /**
     * @param int $descriptionId
     *
     * @return ProductVariantDescription
    */
    private function getDescription(int $descriptionId): ProductVariantDescription
    {
        $description = EntityAssertion::assertExists(
            $this->variantDescriptionRepository->findById($descriptionId),
            $descriptionId,
            ProductVariantDescription::class,
        );

        ProductVariantDescriptionAssertion::assertExists($description);

        return $description;
    }
}
