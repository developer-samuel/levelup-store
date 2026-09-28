<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Renderer;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Domain\{
    Segment\Product\ValueObject\ProductDetailObject,
    Segment\Product\ValueObject\ProductListObject
};

interface ProductRendererContract
{
    /**
     * @param array<string, mixed> $data
     *
     * @return Response
    */
    public function renderProducts(array $data): Response;

    /**
     * @param ProductListObject $data
     *
     * @return Response
    */
    public function renderProductsList(ProductListObject $data): Response;

    /**
     * @param ProductDetailObject $detail
     *
     * @return Response
    */
    public function renderProductDetail(ProductDetailObject $detail): Response;
}
