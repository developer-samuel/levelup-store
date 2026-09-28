<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Review\Renderer;

use Symfony\Component\HttpFoundation\Response;

use Twig\Environment;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\ValueObject\ReviewListObject
};

use App\Core\Application\Segment\Review\Resource\ReviewListWithVariantResource;

use App\Core\Ports\Segment\Review\Renderer\ReviewRendererContract;

final readonly class ReviewRenderer implements ReviewRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param ReviewListObject $list
     * @param ProductVariant $variant
     *
     * @return Response
    */
    public function renderListForVariant(ReviewListObject $list, ProductVariant $variant): Response
    {
        $content = $this->twig->render(
            'features/review/index.html.twig',
            ReviewListWithVariantResource::toArray($list, $variant),
        );

        return new Response($content);
    }
}
