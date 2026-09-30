<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Persistence;

interface EntityPersistenceContract
{
    /**
     * @param object $entity
     * @param bool $flush
     *
     * @return void
    */
    public function persist(object $entity, bool $flush = false): void;

    /**
     * @param object $entity
     * @param bool $flush
     *
     * @return void
    */
    public function remove(object $entity, bool $flush = false): void;

    /**
     * @param object $entity
     * @param bool $flush
     *
     * @return void
    */
    public function refresh(object $entity, bool $flush = false): void;

    /**
     * @return void
    */
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
