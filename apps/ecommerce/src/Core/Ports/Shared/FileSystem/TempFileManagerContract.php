<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\FileSystem;

interface TempFileManagerContract
{
    /**
     * @param string $content
     * @param string $prefix
     * @param string $extension
     *
     * @return string
    */
    public function create(string $content, string $prefix = 'tmp_', string $extension = ''): string;

    /**
     * @param string $path
     *
     * @return string
    */
    public function read(string $path): string;

    /**
     * @param string $path
     *
     * @return void
    */
    public function delete(string $path): void;
}
