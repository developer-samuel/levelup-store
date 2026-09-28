<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

interface ProductRouteQueryContract
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function generateRoute(string $path): string;
}
