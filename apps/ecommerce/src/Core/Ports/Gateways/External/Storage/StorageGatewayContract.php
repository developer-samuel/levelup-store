<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Storage;

interface StorageGatewayContract
{
    public function isEnabled(): bool;
    public function isConnected(): bool;
    public function upload(string $path, string $content): void;
    public function delete(string $path): void;
    public function url(string $path): string;
    public function exists(string $path): bool;
}
