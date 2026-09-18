<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Logging;

use Psr\Log\LoggerInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Shared\Logging\AppLoggerContract;

final readonly class AppLogger implements AppLoggerContract
{
    /**
     * @param LoggerInterface $logger
    */
    public function __construct(
        private LoggerInterface $logger,
    ) {}

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
    ): void {
        $this->log('alert', $message, $throwable, $user, $context);
    }

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
    ): void {
        if ($throwable instanceof \Error) {
            $this->critical('Critical error in ' . $message, $throwable, $user);
            return;
        }

        $this->error('Throwable in ' . $message, $throwable, $user);
    }

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
    ): void {
        $this->log('critical', $message, $throwable, $user, $context);
    }

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
    ): void {
        $this->log('error', $message, $throwable, $user, $context);
    }

    /**
     * @param string $message
     * @param User|null $user
     * @param array<string, mixed> $context
     *
     * @return void
    */
    public function warning(string $message, ?User $user = null, array $context = []): void
    {
        $this->log('warning', $message, null, $user, $context);
    }

    /**
     * @param string $level
     * @param string $message
     * @param \Throwable|null $throwable
     * @param User|null $user
     * @param array<string, mixed> $context
     *
     * @return void
    */
    private function log(
        string $level,
        string $message,
        ?\Throwable $throwable,
        ?User $user,
        array $context = [],
    ): void {
        if ($throwable !== null) {
            $context['throwable'] = $throwable;
        }

        if ($user instanceof User) {
            $context['user'] = $user->getId();
        }

        $this->logger->log($level, $message, $context);
    }
}
