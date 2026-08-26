<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\Search;

use PHPUnit\Framework\TestCase;

use Elastic\{
    Elasticsearch\Client,
    Elasticsearch\Response\Elasticsearch,
    Elasticsearch\Exception\ClientResponseException
};

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;

use App\Adapters\External\Search\ElasticsearchAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\Search\ElasticsearchAdapter
*/
class ElasticsearchAdapterTest extends TestCase
{
    private string $host;
    private int $port;
    private string $testIndex;
    private ElasticsearchAdapter $adapter;

    protected function setUp(): void
    {
        $host = $_ENV['ELASTICSEARCH_HOST'] ?? '127.0.0.1';
        $port = $_ENV['ELASTICSEARCH_PORT'] ?? 9200;

        $this->host      = is_string($host) ? $host : '127.0.0.1';
        $this->port      = is_numeric($port) ? (int) $port : 9200;
        $this->testIndex = 'test_integration_adapter_' . uniqid('', true);
        $this->adapter   = new ElasticsearchAdapter(true, $this->host, $this->port);

        if (!$this->adapter->isConnected()) {
            $this->markTestSkipped('Elasticsearch is not available.');
        }
    }

    protected function tearDown(): void
    {
        if (!$this->adapter->isConnected()) {
            return;
        }

        /** @var Elasticsearch $exists */
        $exists = $this->adapter->getClient()->indices()->exists(['index' => $this->testIndex]);

        if ($exists->asBool()) {
            $this->adapter->getClient()->indices()->delete(['index' => $this->testIndex]);
        }
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ElasticsearchGatewayContract::class, $this->adapter);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        $this->assertTrue($this->adapter->isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenDisabled(): void
    {
        $adapter = new ElasticsearchAdapter(false, $this->host, $this->port);

        $this->assertFalse($adapter->isEnabled());
    }

    public function testIsConnectedReturnsTrueWhenRunning(): void
    {
        $this->assertTrue($this->adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenDisabled(): void
    {
        $adapter = new ElasticsearchAdapter(false, $this->host, $this->port);

        $this->assertFalse($adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenUnreachable(): void
    {
        $adapter = new ElasticsearchAdapter(true, '127.0.0.1', 19999);

        $this->assertFalse($adapter->isConnected());
    }

    public function testGetClientReturnsElasticsearchClient(): void
    {
        $this->assertInstanceOf(Client::class, $this->adapter->getClient());
    }

    public function testEnsureIndexExistsCreatesIndex(): void
    {
        $this->adapter->ensureIndexExists($this->testIndex, [
            'mappings' => [
                'properties' => [
                    'name' => ['type' => 'text'],
                ],
            ],
        ]);

        /** @var Elasticsearch $exists */
        $exists = $this->adapter->getClient()->indices()->exists(['index' => $this->testIndex]);

        $this->assertTrue($exists->asBool());
    }

    public function testEnsureIndexExistsIsIdempotent(): void
    {
        $mapping = [
            'mappings' => [
                'properties' => [
                    'name' => ['type' => 'text'],
                ],
            ],
        ];

        $this->adapter->ensureIndexExists($this->testIndex, $mapping);
        $this->adapter->ensureIndexExists($this->testIndex, $mapping);

        /** @var Elasticsearch $exists */
        $exists = $this->adapter->getClient()->indices()->exists(['index' => $this->testIndex]);

        $this->assertTrue($exists->asBool());
    }

    public function testIndexDocumentIndexesDocument(): void
    {
        $this->adapter->ensureIndexExists($this->testIndex, [
            'mappings' => ['properties' => ['name' => ['type' => 'text']]],
        ]);

        $this->adapter->indexDocument($this->testIndex, 1, ['name' => 'Test Product']);

        $this->adapter->getClient()->indices()->refresh(['index' => $this->testIndex]);

        /** @var Elasticsearch $response */
        $response = $this->adapter->getClient()->get([
            'index' => $this->testIndex,
            'id'    => '1',
        ]);

        /** @var array<string, mixed> $source */
        $source = $response['_source'];
        $this->assertSame('Test Product', $source['name']);
    }

    public function testRemoveDocumentRemovesDocument(): void
    {
        $this->adapter->ensureIndexExists($this->testIndex, [
            'mappings' => ['properties' => ['name' => ['type' => 'text']]],
        ]);

        $this->adapter->indexDocument($this->testIndex, 42, ['name' => 'To Be Removed']);
        $this->adapter->getClient()->indices()->refresh(['index' => $this->testIndex]);

        $this->adapter->removeDocument($this->testIndex, 42);
        $this->adapter->getClient()->indices()->refresh(['index' => $this->testIndex]);

        $this->expectException(ClientResponseException::class);

        $this->adapter->getClient()->get([
            'index' => $this->testIndex,
            'id'    => '42',
        ]);
    }
}
