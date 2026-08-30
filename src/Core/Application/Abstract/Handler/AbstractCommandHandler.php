<?php

declare(strict_types=1);

namespace App\Core\Application\Abstract\Handler;

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Shared\Exception\ConflictException,
    Shared\Exception\NotFoundException,
    Shared\Exception\TooManyRequestsException
};

use App\Core\Ports\Shared\Logging\AppLoggerContract;

use App\Shared\Utils\Formatter\ApiResultFormatter;

abstract class AbstractCommandHandler
{
    /**
     * @param AppLoggerContract $logger
    */
    public function __construct(
        protected readonly AppLoggerContract $logger,
    ) {}

    /**
     * @template T of array<string, mixed>
     *
     * @param callable(): T $callback
     *
     * @return T|array<string, mixed>
    */
    protected function execute(callable $callback): array
    {
        try {
            return $callback();
        } catch (\Throwable $throwable) {
            $response = $this->mapExceptionToApiResponse($throwable);

            if ($response['code'] >= 500) {
                $this->logger->error(
                    'Command handler failed: ' . static::class,
                    $throwable,
                );
            }

            return $response;
        }
    }

    /**
     * @param \Throwable $throwable
     *
     * @return array<string, mixed>
    */
    private function mapExceptionToApiResponse(\Throwable $throwable): array
    {
        return match (true) {
            $throwable instanceof AccessDeniedException    => ApiResultFormatter::error(403, $throwable->getMessage()),
            $throwable instanceof NotFoundException        => ApiResultFormatter::error(404, $throwable->getMessage()),
            $throwable instanceof ConflictException        => ApiResultFormatter::error(409, $throwable->getMessage()),
            $throwable instanceof TooManyRequestsException => ApiResultFormatter::error(429, $throwable->getMessage()),
            $throwable instanceof \DomainException         => ApiResultFormatter::error(422, $throwable->getMessage()),
            default                                        => ApiResultFormatter::error(
                500,
                'Unexpected error occurred. Please check your internet connection and try again.',
            ),
        };
    }
}
