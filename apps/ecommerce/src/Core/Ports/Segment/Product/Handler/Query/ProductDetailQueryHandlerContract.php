<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Handler\Query;

use App\Core\Domain\Segment\Product\ValueObject\ProductDetailObject;

interface ProductDetailQueryHandlerContract
{
    public function handle(string $url): ?ProductDetailObject;
}
