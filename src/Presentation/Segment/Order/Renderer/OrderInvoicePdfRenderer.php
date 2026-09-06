<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Renderer;

use Twig\Environment;

use App\Core\Ports\Segment\Order\Renderer\OrderInvoicePdfRendererContract;

final readonly class OrderInvoicePdfRenderer implements OrderInvoicePdfRendererContract
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
     * @return string
    */
    public function render(array $data): string
    {
        return $this->twig->render('documents/orders-invoice.html.twig', $data);
    }
}
