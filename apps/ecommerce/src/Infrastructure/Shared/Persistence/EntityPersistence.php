<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Persistence;

use Doctrine\ORM\EntityManagerInterface;

use App\Core\Ports\Shared\Persistence\EntityPersistenceContract;

final readonly class EntityPersistence implements EntityPersistenceContract
{
    /**
     * @param EntityManagerInterface $entityManager
    */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @param object $entity
     * @param bool $flush
     *
     * @return void
    */
    public function persist(object $entity, bool $flush = false): void
    {
        $this->entityManager->persist($entity);
        $this->flushIfNeeded($flush);
    }

    /**
     * @param object $entity
     * @param bool $flush
     *
     * @return void
    */
    public function remove(object $entity, bool $flush = false): void
    {
        $this->entityManager->remove($entity);
        $this->flushIfNeeded($flush);
    }

    /**
     * @param object $entity
     * @param bool $flush
     *
     * @return void
    */
    public function refresh(object $entity, bool $flush = false): void
    {
        $this->entityManager->refresh($entity);
        $this->flushIfNeeded($flush);
    }

    /**
     * @return void
    */
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

    /**
     * @param bool $flush
     *
     * @return void
    */
    private function flushIfNeeded(bool $flush = true): void
    {
        if ($flush) {
            $this->flush();
        }
    }
}
