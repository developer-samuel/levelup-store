<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Cart\Renderer;

use Twig\Environment;

use App\Core\Ports\Segment\Cart\Renderer\CartRendererContract;

final readonly class CartRenderer implements CartRendererContract
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return string
    */
    public function renderCart(array $items): string
    {
        return $this->twig->render('layout/public/header/cart/structure/content/list/list.html.twig', [
            'cart' => $items,
        ]);
    }
}
