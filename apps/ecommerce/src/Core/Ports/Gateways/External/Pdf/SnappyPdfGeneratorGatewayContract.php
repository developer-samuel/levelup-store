<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Pdf;

interface SnappyPdfGeneratorGatewayContract
{
    public function generateFromHtml(string $html): string;
}
