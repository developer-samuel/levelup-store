<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Utils\Mapper;

final class ResourceMapper
{
    /**
     * @param object[] $entities
     * @param class-string $class
     * @param mixed ...$extra
     *
     * @return list<array<string, mixed>>
     *
     * @throws \LogicException
    */
    public static function collection(array $entities, string $class, mixed ...$extra): array
    {
        return array_values(
            array_map(
                static fn (object $entity): array => self::mapEntity($entity, $class, ...$extra),
                $entities,
            ),
        );
    }

    /**
     * @param object $entity
     * @param class-string $class
     * @param mixed ...$extra
     *
     * @return array<string, mixed>
     *
     * @throws \LogicException
    */
    private static function mapEntity(object $entity, string $class, mixed ...$extra): array
    {
        self::validateCallback($class);

        $result = $class::toArray($entity, ...$extra);
        if (!is_array($result)) {
            throw new \LogicException(sprintf('Method %s::toArray must return an array.', $class));
        }

        return self::ensureStringKeys($result, $class);
    }

    /**
     * @param class-string $class
     *
     * @return void
     *
     * @throws \LogicException
    */
    private static function validateCallback(string $class): void
    {
        if (!method_exists($class, 'toArray')) {
            throw new \LogicException(sprintf('Method %s::toArray does not exist.', $class));
        }
    }

    /**
     * @param array<mixed> $data
     * @param class-string $class
     *
     * @return array<string, mixed>
     *
     * @throws \LogicException
    */
    private static function ensureStringKeys(array $data, string $class): array
    {
        $validatedData = [];

        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                throw new \LogicException(sprintf('Method %s::toArray returned a non-string key.', $class));
            }

            $validatedData[$key] = $value;
        }

        return $validatedData;
    }
}
