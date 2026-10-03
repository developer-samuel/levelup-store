<?php

declare(strict_types=1);

namespace App\Adapters\Internal\Order;

use App\Core\Ports\{
    Gateways\Internal\Order\OrderInvoiceGatewayContract,
    Gateways\External\Pdf\SnappyPdfGeneratorGatewayContract,
    Web\Segment\Order\Renderer\OrderInvoicePdfRendererContract
};

final readonly class OrderInvoiceAdapter implements OrderInvoiceGatewayContract
{
    public function __construct(
        private SnappyPdfGeneratorGatewayContract $pdfGeneratorAdapter,
        private OrderInvoicePdfRendererContract $renderer,
    ) {}

    /** @param array<string, mixed> $data */
    public function generate(array $data): string
    {
        try {
            $html = $this->renderer->render($data);

            return $this->pdfGeneratorAdapter->generateFromHtml($html);
        } catch (\Throwable $throwable) {
            throw new \Exception(
                'Invoice generation failed: ' . $throwable->getMessage(),
                500,
                $throwable,
            );
        }
    }
}
