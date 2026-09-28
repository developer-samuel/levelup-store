<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Renderer;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\ValueObject\ReviewListObject
};

interface ReviewRendererContract
{
    /**
     * @param ReviewListObject $list
     * @param ProductVariant $variant
     *
     * @return Response
    */
    public function renderListForVariant(ReviewListObject $list, ProductVariant $variant): Response;
}
