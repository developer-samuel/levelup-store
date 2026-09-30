<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Product\Renderer;

use Symfony\Component\HttpFoundation\Response;

use Twig\Environment;

use App\Core\Domain\{
    Segment\Product\ValueObject\ProductDetailObject,
    Segment\Product\ValueObject\ProductListObject
};

use App\Core\Application\Segment\Product\Resource\ProductDetailResource;

use App\Core\Ports\Segment\Product\Renderer\ProductRendererContract;

final readonly class ProductRenderer implements ProductRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param array<string, mixed> $data
     *
     * @return Response
    */
    public function renderProducts(array $data): Response
    {
        $content = $this->twig->render('features/product/catalog/index.html.twig', $data);

        return new Response($content);
    }

    /**
     * @param ProductListObject $data
     *
     * @return Response
    */
    public function renderProductsList(ProductListObject $data): Response
    {
        $content = $this->twig->render(
            'features/product/catalog/content/card/list/list.html.twig',
            $data->toArray(),
        );

        return new Response($content);
    }

    /**
     * @param ProductDetailObject $detail
     *
     * @return Response
    */
    public function renderProductDetail(ProductDetailObject $detail): Response
    {
        $content = $this->twig->render(
            'features/product/detail/show.html.twig',
            ProductDetailResource::toArray($detail),
        );

        return new Response($content);
    }
}
