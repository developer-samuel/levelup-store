<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Utils;

final class MessageResolver
{
    /**
     * @param array<string, mixed> $result
     * @param string|null $message
     *
     * @return string|null
    */
    public static function resolve(array $result, ?string $message = null): ?string
    {
        if (self::isNonEmptyString($message)) {
            return $message;
        }

        $value = $result['message'] ?? null;

        return self::isNonEmptyString($value)
            ? $value
            : null;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @phpstan-assert-if-true string $value
    */
    private static function isNonEmptyString(mixed $value): bool
    {
        return is_string($value) && $value !== '';
    }
}
