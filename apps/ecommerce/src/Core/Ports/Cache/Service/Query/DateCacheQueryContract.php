<?php

declare(strict_types=1);

namespace App\Core\Ports\Cache\Service\Query;

use App\Core\Domain\Cache\DateCacheObject;

interface DateCacheQueryContract
{
    /**
     * @return DateCacheObject
     *
     * @throws \LogicException
    */
    public function getCurrentData(): DateCacheObject;
}
