<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Persistence;

interface EntityPersistenceContract
{
    public function persist(object $entity, bool $flush = false): void;
    public function remove(object $entity, bool $flush = false): void;
    public function refresh(object $entity, bool $flush = false): void;
    public function flush(): void;

    /**
     * @template T
     *
     * @param callable(): T $callback
     *
     * @return T
    */
    public function wrapInTransaction(callable $callback): mixed;
}
