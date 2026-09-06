<?php

declare(strict_types=1);

namespace App\Core\Ports\Cache\Service\Query;

use App\Core\Domain\Cache\FilterCacheObject;

interface FilterCacheQueryContract
{
    /**
     * @param string $queryString
     *
     * @return FilterCacheObject
    */
    public function getVars(string $queryString): FilterCacheObject;
}
