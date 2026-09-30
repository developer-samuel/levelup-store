<?php

declare(strict_types=1);

namespace App\Adapters\External\Pdf;

use Knp\Snappy\Pdf;

use App\Core\Ports\Gateways\External\Pdf\SnappyPdfGeneratorGatewayContract;

final readonly class SnappyPdfGeneratorAdapter implements SnappyPdfGeneratorGatewayContract
{
    /**
     * @param Pdf $pdf
     * @param bool $wkhtmltopdfEnabled
    */
    public function __construct(
        private Pdf $pdf,
        private bool $wkhtmltopdfEnabled,
    ) {}

    /**
     * @param string $html
     *
     * @return string
    */
    public function generateFromHtml(string $html): string
    {
        if (!$this->wkhtmltopdfEnabled) {
            throw new \RuntimeException('PDF generation is disabled.');
        }

        return $this->pdf->getOutputFromHtml($html);
    }
}
