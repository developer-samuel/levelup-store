<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Product;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\ProductStockStatus
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantStockRepositoryContract,
    Shared\Logging\ConsoleLoggerContract
};

use App\Scheduler\{
    Message\Product\ProductStockSyncMessage,
    Task\Abstract\AbstractTask
};

#[AsMessageHandler]
class ProductStockSyncTask extends AbstractTask
{
    /**
     * @param ProductVariantStockRepositoryContract $stockRepository
     * @param EntityManagerInterface $entityManager
     * @param ConsoleLoggerContract $logger
    */
    public function __construct(
        private readonly ProductVariantStockRepositoryContract $stockRepository,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    /**
     * @param ProductStockSyncMessage $message
     *
     * @return void
    */
    public function __invoke(ProductStockSyncMessage $message): void
    {
        $this->execute();
    }

    /**
     * @return string
    */
    protected function getTaskName(): string
    {
        return 'ProductStockSyncTask';
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

        if ($stock->getQuantityAvailable() === 0 && $stock->getStatus() !== ProductStockStatus::OUT_OF_STOCK) {
            $stock->setStatus(ProductStockStatus::OUT_OF_STOCK);

            $this->entityManager->persist($stock);

            return true;
        }

        return false;
    }
}
