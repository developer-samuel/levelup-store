<?php

declare(strict_types=1);

namespace App\Core\Ports\Cache\Service\Query;

use App\Core\Domain\Cache\CoreCacheObject;

interface CoreCacheQueryContract
{
    public function getVars(string $path): CoreCacheObject;
}
