<?php

declare(strict_types=1);

namespace App\Adapters\External\Search;

use Elastic\{
    Elasticsearch\Client,
    Elasticsearch\ClientBuilder,
    Elasticsearch\Response\Elasticsearch
};

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;

final readonly class ElasticsearchAdapter implements ElasticsearchGatewayContract
{
    private Client $client;

    /**
     * @param bool $enabled
     * @param string $host
     * @param int $port
    */
    public function __construct(
        private bool $enabled,
        private string $host,
        private int $port,
    ) {
        $this->client = ClientBuilder::create()
            ->setHosts([$this->host . ':' . $this->port])
            ->build();
    }

    /**
     * @return Client
    */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return bool
    */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
    */
    public function isConnected(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        set_error_handler(static fn() => true);
        $connection = fsockopen($this->host, $this->port, timeout: 3);
        restore_error_handler();

        if ($connection === false) {
            return false;
        }

        fclose($connection);

        return true;
    }

    /**
     * @param string $index
     * @param int $id
     * @param array<string, mixed> $document
     *
     * @return void
    */
    public function indexDocument(string $index, int $id, array $document): void
    {
        $this->client->index([
            'index' => $index,
            'id'    => (string) $id,
            'body'  => $document,
        ]);
    }

    /**
     * @param string $index
     * @param int $id
     *
     * @return void
    */
    public function removeDocument(string $index, int $id): void
    {
        $this->client->delete([
            'index' => $index,
            'id'    => (string) $id,
        ]);
    }

    /**
     * @param string $index
     * @param array<string, mixed> $mapping
     *
     * @return void
    */
    public function ensureIndexExists(string $index, array $mapping): void
    {
        /** @var Elasticsearch $exists */
        $exists = $this->client->indices()->exists(['index' => $index]);

        if ($exists->asBool()) {
            return;
        }

        $this->client->indices()->create([
            'index' => $index,
            'body'  => $mapping,
        ]);
    }

}
