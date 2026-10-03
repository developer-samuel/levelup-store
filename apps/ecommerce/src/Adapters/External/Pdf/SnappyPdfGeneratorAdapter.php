<?php

declare(strict_types=1);

namespace App\Adapters\External\Pdf;

use Knp\Snappy\Pdf;

use App\Core\Ports\Gateways\External\Pdf\SnappyPdfGeneratorGatewayContract;

final readonly class SnappyPdfGeneratorAdapter implements SnappyPdfGeneratorGatewayContract
{
    public function __construct(
        private Pdf $pdf,
        private bool $wkhtmltopdfEnabled,
    ) {}

    public function generateFromHtml(string $html): string
    {
        if (!$this->wkhtmltopdfEnabled) {
            throw new \RuntimeException('PDF generation is disabled.');
        }

        return $this->pdf->getOutputFromHtml($html);
    }
}
