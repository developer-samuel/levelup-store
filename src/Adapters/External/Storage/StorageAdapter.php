<?php

declare(strict_types=1);

namespace App\Adapters\External\Storage;

use Aws\S3\S3Client;

use League\{
    Flysystem\AwsS3V3\AwsS3V3Adapter,
    Flysystem\Filesystem,
    Flysystem\Local\LocalFilesystemAdapter
};

use App\Core\Ports\Gateways\External\Storage\StorageGatewayContract;

final class StorageAdapter implements StorageGatewayContract
{
    private Filesystem $filesystem;

    /**
     * @param bool $minioEnabled
     * @param string $publicUrl
     * @param string $bucket
     * @param string $endpoint
     * @param string $rootUser
     * @param string $rootPassword
     * @param string $uploadsPath
    */
    public function __construct(
        private readonly bool $minioEnabled,
        private readonly string $publicUrl,
        private readonly string $bucket,
        private readonly string $endpoint,
        string $rootUser,
        string $rootPassword,
        string $uploadsPath,
    ) {
        if (!$this->minioEnabled) {
            $this->filesystem = new Filesystem(new LocalFilesystemAdapter($uploadsPath));
            return;
        }

        $client = new S3Client([
            'endpoint'                => $this->endpoint,
            'credentials'             => ['key' => $rootUser, 'secret' => $rootPassword],
            'region'                  => 'us-east-1',
            'version'                 => 'latest',
            'use_path_style_endpoint' => true,
        ]);

        $this->filesystem = new Filesystem(new AwsS3V3Adapter($client, $this->bucket));
    }

    /**
     * @return bool
    */
    public function isEnabled(): bool
    {
        return $this->minioEnabled;
    }

    /**
     * @return bool
    */
    public function isConnected(): bool
    {
        if (!$this->minioEnabled) {
            return false;
        }

        $parsed = parse_url($this->endpoint);
        $host   = $parsed['host'] ?? null;
        $port   = $parsed['port'] ?? (($parsed['scheme'] ?? 'http') === 'https' ? 443 : 80);

        if ($host === null) {
            return false;
        }

        set_error_handler(static fn() => true);
        $connection = fsockopen($host, $port, timeout: 3);
        restore_error_handler();

        if ($connection === false) {
            return false;
        }

        fclose($connection);

        return true;
    }

    /**
     * @param string $path
     * @param string $content
     *
     * @return void
    */
    public function upload(string $path, string $content): void
    {
        $this->filesystem->write($path, $content);
    }

    /**
     * @param string $path
     *
     * @return void
    */
    public function delete(string $path): void
    {
        $this->filesystem->delete($path);
    }

    /**
     * @param string $path
     *
     * @return string
    */
    public function url(string $path): string
    {
        $bare = ltrim($path, '/');
        if (str_starts_with($bare, 'uploads/')) {
            $bare = substr($bare, 8);
        }

        if ($this->minioEnabled) {
            return $this->publicUrl . '/' . $this->bucket . '/uploads/' . $bare;
        }

        return '/uploads/' . $bare;
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    public function exists(string $path): bool
    {
        return $this->filesystem->fileExists($path);
    }
}
