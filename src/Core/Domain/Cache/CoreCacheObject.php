<?php

declare(strict_types=1);

namespace App\Core\Domain\Cache;

final readonly class CoreCacheObject
{
    /**
     * @param string $path
     * @param array<int, string> $guestPaths
     * @param array<int, string> $adminPaths
     * @param bool $isAdminPath
     * @param bool $showHeader
     * @param bool $showFooter
    */
    public function __construct(
        public string $path,
        public array $guestPaths,
        public array $adminPaths,
        public bool $isAdminPath,
        public bool $showHeader,
        public bool $showFooter,
    ) {}
}
