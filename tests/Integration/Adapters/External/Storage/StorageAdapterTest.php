<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\Storage;

use PHPUnit\Framework\TestCase;

use App\Core\Ports\Gateways\External\Storage\StorageGatewayContract;

use App\Adapters\External\Storage\StorageAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\Storage\StorageAdapter
*/
class StorageAdapterTest extends TestCase
{
    private string $endpoint;
    private string $publicUrl;
    private string $bucket;
    private string $rootUser;
    private string $rootPassword;
    private string $uploadsPath;
    private StorageAdapter $adapter;

    protected function setUp(): void
    {
        $endpoint     = $_ENV['MINIO_ENDPOINT']       ?? 'http://127.0.0.1:9000';
        $publicUrl    = $_ENV['MINIO_PUBLIC_URL']     ?? 'http://127.0.0.1:9000';
        $bucket       = $_ENV['MINIO_BUCKET']         ?? 'levelup-store';
        $rootUser     = $_ENV['MINIO_ROOT_USER']      ?? 'minioadmin';
        $rootPassword = $_ENV['MINIO_ROOT_PASSWORD']  ?? 'minioadmin';

        $this->endpoint     = is_string($endpoint)     ? $endpoint     : 'http://127.0.0.1:9000';
        $this->publicUrl    = is_string($publicUrl)    ? $publicUrl    : 'http://127.0.0.1:9000';
        $this->bucket       = is_string($bucket)       ? $bucket       : 'levelup-store';
        $this->rootUser     = is_string($rootUser)     ? $rootUser     : 'minioadmin';
        $this->rootPassword = is_string($rootPassword) ? $rootPassword : 'minioadmin';
        $this->uploadsPath  = sys_get_temp_dir() . '/levelup_test_uploads';

        $this->adapter = new StorageAdapter(
            true,
            $this->publicUrl,
            $this->bucket,
            $this->endpoint,
            $this->rootUser,
            $this->rootPassword,
            $this->uploadsPath,
        );
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(StorageGatewayContract::class, $this->adapter);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        $this->assertTrue($this->adapter->isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenDisabled(): void
    {
        $adapter = new StorageAdapter(
            false,
            $this->publicUrl,
            $this->bucket,
            $this->endpoint,
            $this->rootUser,
            $this->rootPassword,
            $this->uploadsPath,
        );

        $this->assertFalse($adapter->isEnabled());
    }

    public function testIsConnectedReturnsTrueWhenRunning(): void
    {
        $this->skipIfNotConnected();
        $this->assertTrue($this->adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenDisabled(): void
    {
        $adapter = new StorageAdapter(
            false,
            $this->publicUrl,
            $this->bucket,
            $this->endpoint,
            $this->rootUser,
            $this->rootPassword,
            $this->uploadsPath,
        );

        $this->assertFalse($adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenUnreachable(): void
    {
        $adapter = new StorageAdapter(
            true,
            $this->publicUrl,
            $this->bucket,
            'http://127.0.0.1:19999',
            $this->rootUser,
            $this->rootPassword,
            $this->uploadsPath,
        );

        $this->assertFalse($adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenEndpointIsMalformed(): void
    {
        $adapter = new StorageAdapter(
            true,
            $this->publicUrl,
            $this->bucket,
            'not-a-valid-url',
            $this->rootUser,
            $this->rootPassword,
            $this->uploadsPath,
        );

        $this->assertFalse($adapter->isConnected());
    }

    public function testUrlReturnsMinioUrlWhenEnabled(): void
    {
        $this->skipIfNotConnected();
        $url = $this->adapter->url('test/image.jpg');

        $this->assertStringContainsString($this->bucket, $url);
        $this->assertStringContainsString('image.jpg', $url);
    }

    public function testUrlStripsUploadsPrefixWhenEnabled(): void
    {
        $this->skipIfNotConnected();
        $url = $this->adapter->url('uploads/test/image.jpg');

        $this->assertStringContainsString($this->bucket, $url);
        $this->assertStringContainsString('image.jpg', $url);
        $this->assertStringNotContainsString('uploads/uploads/', $url);
    }

    public function testUrlReturnsLocalUrlWhenDisabled(): void
    {
        $adapter = new StorageAdapter(
            false,
            $this->publicUrl,
            $this->bucket,
            $this->endpoint,
            $this->rootUser,
            $this->rootPassword,
            $this->uploadsPath,
        );

        $url = $adapter->url('test/image.jpg');

        $this->assertStringStartsWith('/uploads/', $url);
    }

    public function testUploadWritesToStorage(): void
    {
        $this->skipIfNotConnected();
        $path = 'phpunit/test-upload-' . uniqid() . '.txt';

        $this->adapter->upload($path, 'PHPUnit test content');

        $this->assertTrue($this->adapter->exists($path));
        $this->adapter->delete($path);
    }

    public function testDeleteRemovesFileFromStorage(): void
    {
        $this->skipIfNotConnected();
        $path = 'phpunit/test-delete-' . uniqid() . '.txt';

        $this->adapter->upload($path, 'to be deleted');
        $this->adapter->delete($path);

        $this->assertFalse($this->adapter->exists($path));
    }

    public function testExistsReturnsFalseForNonExistentFile(): void
    {
        $this->skipIfNotConnected();
        $this->assertFalse($this->adapter->exists('phpunit/nonexistent-' . uniqid() . '.txt'));
    }

    private function skipIfNotConnected(): void
    {
        if (!$this->adapter->isConnected()) {
            $this->markTestSkipped('MinIO is not available.');
        }
    }
}
