<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\FileSystem;

interface TempFileManagerContract
{
    public function create(string $content, string $prefix = 'tmp_', string $extension = ''): string;
    public function read(string $path): string;
    public function delete(string $path): void;
}
