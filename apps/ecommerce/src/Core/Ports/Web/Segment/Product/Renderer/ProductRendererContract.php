<?php

declare(strict_types=1);

namespace App\Core\Ports\Web\Segment\Product\Renderer;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Domain\{
    Segment\Product\ValueObject\ProductDetailObject,
    Segment\Product\ValueObject\ProductListObject
};

interface ProductRendererContract
{
    /** @param array<string, mixed> $data */
    public function renderProducts(array $data): Response;

    public function renderProductsList(ProductListObject $data): Response;
    public function renderProductDetail(ProductDetailObject $detail): Response;
}
