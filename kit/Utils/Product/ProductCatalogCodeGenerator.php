<?php

declare(strict_types=1);

namespace Kit\Utils\Product;

use Kit\Utils\Shared\IdentifierGenerator;

final class ProductCatalogCodeGenerator
{
    /**
     * @param string $productName
     * @param string|null $variantName
     * @param int $randomLength
     *
     * @return string
    */
    public static function generateCatalogCode(string $productName, ?string $variantName = null, int $randomLength = 10): string
    {
        $prefix = IdentifierGenerator::generatePrefix($productName);

        $variantPart = $variantName ? '-' . strtoupper(str_replace(' ', '', $variantName)) : '';

        $uniqueSuffix = '-' . IdentifierGenerator::generateRandomAlphanumeric($randomLength);

        return $prefix . $variantPart . $uniqueSuffix;
    }
}
