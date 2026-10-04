<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Persistence;

use Doctrine\ORM\EntityManagerInterface;

use App\Core\Ports\Shared\Persistence\EntityPersistenceContract;

final readonly class EntityPersistence implements EntityPersistenceContract
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function persist(object $entity, bool $flush = false): void
    {
        $this->entityManager->persist($entity);
        $this->flushIfNeeded($flush);
    }

    public function remove(object $entity, bool $flush = false): void
    {
        $this->entityManager->remove($entity);
        $this->flushIfNeeded($flush);
    }

    public function refresh(object $entity, bool $flush = false): void
    {
        $this->entityManager->refresh($entity);
        $this->flushIfNeeded($flush);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @template T
     *
     * @param callable(): T $callback
     *
     * @return T
    */
    public function wrapInTransaction(callable $callback): mixed
    {
        return $this->entityManager->wrapInTransaction($callback);
    }

    private function flushIfNeeded(bool $flush = true): void
    {
        if ($flush) {
            $this->flush();
        }
    }
}
