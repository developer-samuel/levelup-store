<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Shared;

final class CacheAssertion
{
    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
    */
    public static function assertValidType(mixed $data, string $className): object
    {
        if (!$data instanceof $className) {
            throw new \LogicException(
                sprintf('Cache returned invalid data type. Expected %s.', $className),
            );
        }

        /** @var T $data */
        return $data;
    }
}
