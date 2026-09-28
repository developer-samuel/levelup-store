<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Twig;

use Twig\Environment;

final readonly class CartStateClearer
{
    /**
     * @param Environment $twig
    */
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @return void
    */
    public function clear(): void
    {
        $this->twig->addGlobal('cart', []);
        $this->twig->addGlobal('totalItems', 0);
        $this->twig->addGlobal('totalPrice', 0);
    }
}
