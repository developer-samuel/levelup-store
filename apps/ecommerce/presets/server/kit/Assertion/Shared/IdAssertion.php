<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Shared;

use Packages\Kit\Utils\Shared\DataSanitizer;

final class IdAssertion
{
    /** @param class-string<\Throwable> $exceptionClass */
    public static function assert(
        ?int $id,
        string $name = 'ID',
        string $exceptionClass = \RuntimeException::class,
    ): int {
        $id = DataSanitizer::sanitizeInt($id);
        if ($id === null) {
            throw new $exceptionClass($name . ' is missing or invalid.');
        }

        return $id;
    }

    public static function assertNumeric(int|string|null $id, string $name = 'ID'): void
    {
        if (DataSanitizer::sanitizeInt($id) === null) {
            throw new \InvalidArgumentException('Invalid ' . $name);
        }
    }

    public static function assertType(mixed $idRaw, string $name = 'ID'): void
    {
        if (!is_int($idRaw) && !is_string($idRaw) && !is_null($idRaw)) {
            throw new \InvalidArgumentException(sprintf(
                '%s must be int, string or null, %s given.',
                $name,
                get_debug_type($idRaw),
            ));
        }
    }
}
