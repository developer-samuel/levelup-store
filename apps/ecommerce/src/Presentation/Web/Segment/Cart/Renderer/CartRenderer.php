<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Cart\Renderer;

use Twig\Environment;

use App\Core\Ports\Web\Segment\Cart\Renderer\CartRendererContract;

final readonly class CartRenderer implements CartRendererContract
{
    public function __construct(
        private Environment $twig,
    ) {}

    /** @param array<int, array<string, mixed>> $items */
    public function renderCart(array $items): string
    {
        return $this->twig->render('layout/public/header/cart/structure/content/list/list.html.twig', [
            'cart' => $items,
        ]);
    }
}
