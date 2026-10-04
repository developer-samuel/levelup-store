<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Logging;

use App\Core\Domain\Segment\User\Entity\User;

interface AppLoggerContract
{
    /** @param array<string, mixed> $context */
    public function alert(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /** @param array<string, mixed> $context */
    public function logThrowable(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /** @param array<string, mixed> $context */
    public function critical(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /** @param array<string, mixed> $context */
    public function error(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /** @param array<string, mixed> $context */
    public function warning(string $message, ?User $user = null, array $context = []): void;
}
