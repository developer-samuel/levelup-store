<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Product\Service\Command;

use Kit\{
    Assertion\Domain\Product\Variant\ProductVariantEanAssertion,
    Assertion\Shared\EntityAssertion
};

use App\Core\Domain\{
    Admin\Product\Payload\AdminVariantEanPayload,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

use App\Core\Application\Admin\Segment\Product\Service\Command\Abstract\AbstractAdminVariantCommandService;

use App\Core\Ports\{
    Admin\Segment\Product\Service\Command\AdminVariantEanCommandContract,
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

class AdminVariantEanCommandService extends AbstractAdminVariantCommandService implements AdminVariantEanCommandContract
{
    /**
     * @param ProductVariantEanRepositoryContract $variantEanRepository
     * @param EntityPersistenceContract $entityPersistence
     * @param ProductVariantRepositoryContract $variantRepository
    */
    public function __construct(
        private readonly ProductVariantEanRepositoryContract $variantEanRepository,
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
     * @param AdminVariantEanPayload $payload
     *
     * @return ProductVariantEan
    */
    public function createEan(int $variantId, AdminVariantEanPayload $payload): ProductVariantEan
    {
        $variant = $this->resolveVariant($variantId);

        $ean = $this->buildEanEntity($variant, $payload);

        $this->adjustStockForCreate($variant);

        $this->saveEntityWithVariant($ean, $variant);

        return $ean;
    }

    /**
     * @param int $eanId
     * @param int $variantId
     * @param AdminVariantEanPayload $payload
     *
     * @return ProductVariantEan
    */
    public function updateEan(int $eanId, int $variantId, AdminVariantEanPayload $payload): ProductVariantEan
    {
        $this->resolveVariant($variantId);

        $ean = $this->getEan($eanId);

        $this->updateEanEntity($ean, $payload);

        $this->entityPersistence->persist($ean, true);

        return $ean;
    }

    /**
     * @param ProductVariantEan $ean
     *
     * @return void
    */
    public function destroyEan(ProductVariantEan $ean): void
    {
        $adjustedStock = $this->adjustStockForDestroy($ean);

        if ($adjustedStock) {
            $this->entityPersistence->persist($adjustedStock, true);
        }

        $this->entityPersistence->remove($ean, true);
    }

    /**
     * @param ProductVariant $variant
     * @param AdminVariantEanPayload $payload
     *
     * @return ProductVariantEan
    */
    private function buildEanEntity(ProductVariant $variant, AdminVariantEanPayload $payload): ProductVariantEan
    {
        return (new ProductVariantEan())
            ->setVariant($variant)
            ->setCode($payload->code);
    }

    /**
     * @param ProductVariantEan $ean
     * @param AdminVariantEanPayload $payload
     *
     * @return void
    */
    private function updateEanEntity(ProductVariantEan $ean, AdminVariantEanPayload $payload): void
    {
        $ean->setCode($payload->code)
            ->setUpdatedAt();
    }

    /**
     * @param ProductVariantEan $ean
     *
     * @return ProductVariantStock|null
    */
    private function adjustStockForDestroy(ProductVariantEan $ean): ?ProductVariantStock
    {
        $stock = $ean->getVariant()->getStock();
        if (!$stock) {
            return null;
        }

        match ($ean->getStatus()) {
            ProductVariantEanStatus::ACTIVE   => $stock->setQuantityAvailable(max(0, $stock->getQuantityAvailable() - 1)),
            ProductVariantEanStatus::RESERVED => $stock->setQuantityReserved(max(0, $stock->getQuantityReserved() - 1)),
            ProductVariantEanStatus::REFUNDED => $stock->setQuantityRefunded(max(0, $stock->getQuantityRefunded() - 1)),
            ProductVariantEanStatus::SOLD     => null,
        };

        return $stock;
    }

    /**
     * @param ProductVariant $variant
     *
     * @return void
    */
    private function adjustStockForCreate(ProductVariant $variant): void
    {
        $stock = $variant->getStock();
        if ($stock) {
            $stock->setQuantityAvailable($stock->getQuantityAvailable() + 1);
        }
    }

    /**
     * @param int $eanId
     *
     * @return ProductVariantEan
    */
    private function getEan(int $eanId): ProductVariantEan
    {
        $ean = EntityAssertion::assertExists(
            $this->variantEanRepository->findById($eanId),
            $eanId,
            ProductVariantEan::class,
        );

        ProductVariantEanAssertion::assertExists($ean);

        return $ean;
    }
}
