<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Traits;

trait IterableCollector
{
    /**
     * @template T of object
     *
     * @param iterable<mixed> $items
     * @param class-string<T> $className
     *
     * @return list<T>
    */
    private function collectFromIterable(iterable $items, string $className): array
    {
        /** @var list<T> $results */
        $results = iterator_to_array(
            $this->iterateOfClass($items, $className),
            false,
        );

        return $results;
    }

    /**
     * @template T of object
     *
     * @param iterable<mixed> $items
     * @param class-string<T> $className
     *
     * @return iterable<T>
    */
    private function iterateOfClass(iterable $items, string $className): iterable
    {
        foreach ($items as $item) {
            if ($item instanceof $className) {
                /** @var T $item */
                yield $item;
            }
        }
    }
}
