<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Abstract;

use Doctrine\ORM\EntityManagerInterface;

use App\Core\Ports\Shared\Logging\ConsoleLoggerContract;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/** @template TItem */
abstract class AbstractTask
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly ConsoleLoggerContract $logger,
    ) {}

    abstract protected function getTaskName(): string;

    /** @return iterable<mixed> */
    abstract protected function fetchItems(): iterable;

    /** @param TItem $item */
    protected function processSingleItem(mixed $item): bool
    {
        throw new \LogicException(static::class . ' must implement processSingleItem() or override processItems()');
    }

    /** @param iterable<TItem> $items */
    protected function processItems(iterable $items): int
    {
        $updatedCount = 0;

        foreach ($items as $item) {
            if ($this->processSingleItem($item)) {
                $updatedCount++;
            }
        }

        if ($updatedCount > 0) {
            $this->entityManager->flush();
        }

        return $updatedCount;
    }

    protected function logResult(int $total): void
    {
        if ($total > 0) {
            $this->logger->logSuccess(
                '[' . DateTimeFormatter::format(new \DateTimeImmutable()) . sprintf('] Processed total %d items', $total),
            );
            return;
        }

        $this->logger->logMessage(
            '[' . DateTimeFormatter::format(new \DateTimeImmutable()) . "] No items to process",
        );
    }

    protected function log(string $message): void
    {
        $this->logger->logMessage('[' . DateTimeFormatter::format(new \DateTimeImmutable()) . ('] ' . $message));
    }

    final public function execute(): void
    {
        $this->log('Starting ' . $this->getTaskName());

        $items = $this->fetchItems();
        $total = $this->processItems($items);

        $this->logResult($total);
    }
}
