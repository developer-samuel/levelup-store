<?php

declare(strict_types=1);

namespace App\Core\Domain\Cache;

final readonly class FilterCacheObject
{
    /**
     * @param list<string> $subtypesActive
     * @param list<string> $brandsActive
     * @param int $step
    */
    public function __construct(
        public array $subtypesActive,
        public array $brandsActive,
        public int $step,
    ) {}
}
