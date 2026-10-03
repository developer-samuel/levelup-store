<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

interface ProductRouteQueryContract
{
    public function generateRoute(string $path): string;
}
