<?php

declare(strict_types=1);

namespace Kit\Assertion\Shared;

final class CacheAssertion
{
    /**
     * @template T of object
     *
     * @param mixed $data
     * @param class-string<T> $className
     *
     * @return T
     *
     * @throws \LogicException
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
