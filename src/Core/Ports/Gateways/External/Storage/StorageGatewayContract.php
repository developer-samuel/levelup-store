<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Storage;

interface StorageGatewayContract
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
     * @param string $path
     * @param string $content
     *
     * @return void
    */
    public function upload(string $path, string $content): void;

    /**
     * @param string $path
     *
     * @return void
    */
    public function delete(string $path): void;

    /**
     * @param string $path
     *
     * @return string
    */
    public function url(string $path): string;

    /**
     * @param string $path
     *
     * @return bool
    */
    public function exists(string $path): bool;
}
