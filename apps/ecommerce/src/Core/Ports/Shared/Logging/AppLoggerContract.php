<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Logging;

use App\Core\Domain\Segment\User\Entity\User;

interface AppLoggerContract
{
    /**
     * @param string $message
     * @param \Throwable|null $throwable
     * @param User|null $user
     * @param array<string, mixed> $context
     *
     * @return void
    */
    public function alert(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /**
     * @param string $message
     * @param \Throwable|null $throwable
     * @param User|null $user
     * @param array<string, mixed> $context
     *
     * @return void
    */
    public function logThrowable(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /**
     * @param string $message
     * @param \Throwable|null $throwable
     * @param User|null $user
     * @param array<string, mixed> $context
     *
     * @return void
    */
    public function critical(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /**
     * @param string $message
     * @param \Throwable|null $throwable
     * @param User|null $user
     * @param array<string, mixed> $context
     *
     * @return void
    */
    public function error(
        string $message,
        ?\Throwable $throwable = null,
        ?User $user = null,
        array $context = [],
    ): void;

    /**
     * @param string $message
     * @param User|null $user
     * @param array<string, mixed> $context
     *
     * @return void
    */
    public function warning(string $message, ?User $user = null, array $context = []): void;
}
