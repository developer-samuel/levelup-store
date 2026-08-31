<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Abstract;

use App\Core\Application\Shared\Utils\Mapper\ResourceMapper;

use App\Core\Ports\Shared\Logging\AppLoggerContract;

abstract class AbstractAdminApiListQueryHandler
{
    /**
     * @param AppLoggerContract $logger
    */
    public function __construct(
        protected readonly AppLoggerContract $logger,
    ) {}

    /**
     * @param array<string, mixed> $context
     *
     * @return array<int, object>
    */
    abstract protected function getRepositoryClass(array $context = []): array;

    /**
     * @return class-string
    */
    abstract protected function getResourceClass(): string;

    /**
     * @param array<string, mixed> $context
     *
     * @return array<int, array<string, mixed>>
    */
    public function handle(array $context = []): array
    {
        try {
            $items = $this->getRepositoryClass($context);

            return $this->mapAndReindex($items, $this->getResourceClass());
        } catch (\Exception $exception) {
            $this->logger->error(
                'Query handler failed: ' . get_class($this),
                $exception,
            );

            throw $exception;
        }
    }

    /**
     * @param array<int, object> $items
     * @param class-string $resourceClass
     *
     * @return array<int, array<string, mixed>>
    */
    private function mapAndReindex(array $items, string $resourceClass): array
    {
        $mapped = ResourceMapper::collection($items, $resourceClass);

        return array_values($mapped);
    }
}
