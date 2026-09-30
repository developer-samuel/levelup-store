<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Renderer;

interface CartRendererContract
{
    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return string
    */
    public function renderCart(array $items): string;
}
