<?php

declare(strict_types=1);

namespace Kit\Assertion\Shared;

final class EntityAssertion
{
    /**
     * @template T of object
     *
     * @param T|null $entity
     * @param int|string $id
     * @param class-string<T> $className
     *
     * @return T
     *
     * @throws \RuntimeException
    */
    public static function assertExists(
        ?object $entity,
        int|string $id,
        string $className,
    ): object {
        if ($entity === null) {
            throw new \RuntimeException(
                sprintf('%s with identifier "%s" was not found.', $className, (string) $id),
            );
        }

        if (!$entity instanceof $className) {
            throw new \RuntimeException(
                sprintf(
                    'Expected instance of %s, got %s.',
                    $className,
                    get_debug_type($entity),
                ),
            );
        }

        return $entity;
    }
}
