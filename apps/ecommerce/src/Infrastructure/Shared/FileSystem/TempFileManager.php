<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\FileSystem;

use App\Core\Ports\Shared\FileSystem\TempFileManagerContract;

final class TempFileManager implements TempFileManagerContract
{
    /**
     * @param string $content
     * @param string $prefix
     * @param string $extension
     *
     * @return string
    */
    public function create(string $content, string $prefix = 'tmp_', string $extension = ''): string
    {
        $path = tempnam(sys_get_temp_dir(), $prefix) . $extension;
        file_put_contents($path, $content);

        return $path;
    }

    /**
     * @param string $path
     *
     * @return string
    */
    public function read(string $path): string
    {
        $content = file_get_contents($path);

        return $content !== false ? $content : '';
    }

    /**
     * @param string $path
     *
     * @return void
    */
    public function delete(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
