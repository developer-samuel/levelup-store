<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Product;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Product\{
    Entity\Variant\ProductVariantRecommended,
    Specification\ProductVariantAvailabilitySpecification
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRecommendedRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\ConsoleLoggerContract
};

use App\Scheduler\{
    Message\Product\ProductRecommendedSyncMessage,
    Task\Abstract\AbstractTask
};

#[AsMessageHandler]
class ProductRecommendedSyncTask extends AbstractTask
{
    /**
     * @param ProductVariantRecommendedRepositoryContract $recommendedRepository
     * @param ProductVariantRepositoryContract $variantRepository
     * @param EntityManagerInterface $entityManager
     * @param ConsoleLoggerContract $logger
    */
    public function __construct(
        private readonly ProductVariantRecommendedRepositoryContract $recommendedRepository,
        private readonly ProductVariantRepositoryContract $variantRepository,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    /**
     * @param ProductRecommendedSyncMessage $message
     *
     * @return void
    */
    public function __invoke(ProductRecommendedSyncMessage $message): void
    {
        $this->execute();
    }

    /**
     * @return string
    */
    protected function getTaskName(): string
    {
        return 'ProductRecommendedSyncTask';
    }

    /**
     * @return ProductVariantRecommended[]
    */
    protected function fetchItems(): iterable
    {
        return $this->recommendedRepository->findAll();
    }

    /**
     * @param iterable<ProductVariantRecommended> $items
     *
     * @return int
    */
    protected function processItems(iterable $items): int
    {
        $count = 0;
        $currentVariantIds = $this->getCurrentVariantIds();

        foreach ($items as $recommended) {
            if (ProductVariantAvailabilitySpecification::findOneInStock($recommended->getVariant()) !== null) {
                continue;
            }

            $replacement = $this->variantRepository->findRandomAvailableExcluding($currentVariantIds);

            if ($replacement === null) {
                continue;
            }

            $recommended->setVariant($replacement);
            $this->entityManager->persist($recommended);

            $currentVariantIds[] = $replacement->getId();
            $count++;
        }

        if ($count > 0) {
            $this->entityManager->flush();
        }

        return $count;
    }

    /**
     * @return int[]
    */
    private function getCurrentVariantIds(): array
    {
        return array_map(
            static fn(ProductVariantRecommended $r): int => $r->getVariant()->getId(),
            $this->recommendedRepository->findAll(),
        );
    }
}
