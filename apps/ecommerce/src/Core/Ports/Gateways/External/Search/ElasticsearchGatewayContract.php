<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Search;

interface ElasticsearchGatewayContract
{
    public function isEnabled(): bool;
    public function isConnected(): bool;

    /** @param array<string, mixed> $document */
    public function indexDocument(string $index, int $id, array $document): void;

    public function removeDocument(string $index, int $id): void;

    /** @param array<string, mixed> $mapping */
    public function ensureIndexExists(string $index, array $mapping): void;
}
