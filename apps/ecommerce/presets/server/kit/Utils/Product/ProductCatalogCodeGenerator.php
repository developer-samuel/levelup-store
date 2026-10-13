<?php

declare(strict_types=1);

namespace Presets\Kit\Utils\Product;

use Presets\Kit\Utils\Shared\IdentifierGenerator;

final class ProductCatalogCodeGenerator
{
    public static function generateCatalogCode(string $productName, ?string $variantName = null, int $randomLength = 10): string
    {
        $prefix = IdentifierGenerator::generatePrefix($productName);

        $variantPart = $variantName !== null ? '-' . strtoupper(str_replace(' ', '', $variantName)) : '';

        $uniqueSuffix = '-' . IdentifierGenerator::generateRandomAlphanumeric($randomLength);

        return $prefix . $variantPart . $uniqueSuffix;
    }
}
