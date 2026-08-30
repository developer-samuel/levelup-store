<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Product;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantStockRepositoryContract,
    Shared\Logging\ConsoleLoggerContract
};

use App\Scheduler\{
    Abstract\AbstractTask,
    Message\Product\ProductEanSyncMessage
};

#[AsMessageHandler]
class ProductEanSyncTask extends AbstractTask
{
    /**
     * @param ProductVariantStockRepositoryContract $stockRepository
     * @param ProductVariantEanRepositoryContract $eanRepository
     * @param EntityManagerInterface $entityManager
     * @param ConsoleLoggerContract $logger
    */
    public function __construct(
        private readonly ProductVariantStockRepositoryContract $stockRepository,
        private readonly ProductVariantEanRepositoryContract $eanRepository,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    /**
     * @param ProductEanSyncMessage $message
     *
     * @return void
    */
    public function __invoke(ProductEanSyncMessage $message): void
    {
        $this->execute();
    }

    /**
     * @return string
    */
    protected function getTaskName(): string
    {
        return 'ProductEanSyncTask';
    }

    /**
     * @return iterable<ProductVariantStock>
    */
    protected function fetchItems(): iterable
    {
        return $this->stockRepository->findAll();
    }

    /**
     * @param ProductVariantStock $stock
     *
     * @return bool
    */
    protected function processSingleItem(mixed $stock): bool
    {
        assert($stock instanceof ProductVariantStock);

        $quantities = $this->calculateQuantities($stock);

        if (!$this->needsUpdate($stock, $quantities)) {
            return false;
        }

        $this->updateStock($stock, $quantities);
        $this->entityManager->persist($stock);

        return true;
    }

    /**
     * @param ProductVariantStock $stock
     *
     * @return array<string, int>
    */
    private function calculateQuantities(ProductVariantStock $stock): array
    {
        $variant = $stock->getVariant();

        return [
            'available' => $this->countByEanStatus($variant, ProductVariantEanStatus::ACTIVE),
            'reserved'  => $this->countByEanStatus($variant, ProductVariantEanStatus::RESERVED),
            'refunded'  => $this->countByEanStatus($variant, ProductVariantEanStatus::REFUNDED),
        ];
    }

    /**
     * @param ProductVariant $variant
     * @param ProductVariantEanStatus $status
     *
     * @return int
    */
    private function countByEanStatus(ProductVariant $variant, ProductVariantEanStatus $status): int
    {
        $items = iterator_to_array(
            $this->eanRepository->findAllByVariantAndStatus($variant, $status),
            false,
        );

        return count($items);
    }

    /**
     * @param ProductVariantStock $stock
     * @param array<string, int> $quantities
     *
     * @return bool
    */
    private function needsUpdate(ProductVariantStock $stock, array $quantities): bool
    {
        if ($stock->getQuantityAvailable() !== $quantities['available']) {
            return true;
        }

        if ($stock->getQuantityReserved() !== $quantities['reserved']) {
            return true;
        }

        return $stock->getQuantityRefunded() !== $quantities['refunded'];
    }

    /**
     * @param ProductVariantStock $stock
     * @param array<string, int> $quantities
     *
     * @return void
    */
    private function updateStock(ProductVariantStock $stock, array $quantities): void
    {
        $stock->setQuantityAvailable($quantities['available']);
        $stock->setQuantityReserved($quantities['reserved']);
        $stock->setQuantityRefunded($quantities['refunded']);
    }
}
