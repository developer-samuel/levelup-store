<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Cart;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Cart\Entity\Cart;

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Shared\Logging\ConsoleLoggerContract
};

use App\Scheduler\{
    Message\Cart\CartCleanupMessage,
    Task\Abstract\AbstractTask
};

#[AsMessageHandler]
class CartCleanupTask extends AbstractTask
{
    private const INACTIVE_DAYS = 7;

    /**
     * @param CartRepositoryContract $cartRepository
     * @param EntityManagerInterface $entityManager
     * @param ConsoleLoggerContract $logger
    */
    public function __construct(
        private readonly CartRepositoryContract $cartRepository,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    /**
     * @param CartCleanupMessage $message
     *
     * @return void
    */
    public function __invoke(CartCleanupMessage $message): void
    {
        $this->execute();
    }

    /**
     * @return string
    */
    protected function getTaskName(): string
    {
        return 'CartCleanupTask';
    }

    /**
     * @return Cart[]
    */
    protected function fetchItems(): iterable
    {
        $threshold = new \DateTimeImmutable(sprintf('-%d days', self::INACTIVE_DAYS));

        return $this->cartRepository->findInactiveSince($threshold);
    }

    /**
     * @param iterable<Cart> $items
     *
     * @return int
    */
    protected function processItems(iterable $items): int
    {
        $count = 0;

        foreach ($items as $cart) {
            $this->entityManager->remove($cart);
            $count++;
        }

        $count += $this->removeEmptyCarts();

        if ($count > 0) {
            $this->entityManager->flush();
        }

        return $count;
    }

    /**
     * @return int
    */
    private function removeEmptyCarts(): int
    {
        $emptyCarts = $this->cartRepository->findEmpty();

        foreach ($emptyCarts as $cart) {
            $this->entityManager->remove($cart);
        }

        return count($emptyCarts);
    }
}
