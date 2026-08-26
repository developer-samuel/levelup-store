<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Search;

interface ElasticsearchGatewayContract
{
    /**
     * @return bool
    */
    public function isEnabled(): bool;

    /**
     * @return bool
    */
    public function isConnected(): bool;

    /**
     * @param string $index
     * @param int $id
     * @param array<string, mixed> $document
     *
     * @return void
    */
    public function indexDocument(string $index, int $id, array $document): void;

    /**
     * @param string $index
     * @param int $id
     *
     * @return void
    */
    public function removeDocument(string $index, int $id): void;

    /**
     * @param string $index
     * @param array<string, mixed> $mapping
     *
     * @return void
    */
    public function ensureIndexExists(string $index, array $mapping): void;

}
