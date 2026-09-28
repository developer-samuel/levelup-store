<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Assembler;

use Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductVariantObject
};

use App\Core\Application\{
    Segment\Product\Factory\ProductVariantFactory,
    Segment\Product\Resource\ProductVariantResource
};

use App\Core\Ports\{
    Segment\Product\Assembler\ProductVariantAssemblerContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

/**
 * @phpstan-type NormalizedPrices array{
 *     price: float,
 *     discount: ?float,
 *     discountPrice: float
 * }
*/
final readonly class ProductVariantAssembler implements ProductVariantAssemblerContract
{
    /**
     * @param ProductVariantFactory $variantFactory
    */
    public function __construct(
        private ProductVariantFactory $variantFactory,
    ) {}

    /**
     * @param ProductVariant $variant
     * @param ReviewQueryContract $reviewQuery
     *
     * @return ProductVariantObject
     *
     * @throws \LogicException
    */
    public function toObject(
        ProductVariant $variant,
        ReviewQueryContract $reviewQuery,
    ): ProductVariantObject {
        $variantId = IdAssertion::assert($variant->getId(), 'Variant ID', \LogicException::class);

        $prices = self::normalizePrices(ProductVariantResource::extractPrices($variant));
        $averageRating = $reviewQuery->getAverageRatingByVariant($variantId);

        return $this->createProductVariantObject($variant, $prices, $averageRating);
    }

    /**
     * @param ProductVariant $variant
     * @param array<string, float> $prices
     * @param float $averageRating
     *
     * @return ProductVariantObject
    */
    private function createProductVariantObject(
        ProductVariant $variant,
        array $prices,
        float $averageRating,
    ): ProductVariantObject {
        return $this->variantFactory->fromObject(
            $variant,
            $prices,
            $averageRating,
        );
    }

    /**
     * @param NormalizedPrices $prices
     *
     * @return array<string, float>
    */
    private function normalizePrices(array $prices): array
    {
        return [
            'price'         => (float) $prices['price'],
            'discount'      => (float) ($prices['discount'] ?? 0.0),
            'discountPrice' => (float) $prices['discountPrice'],
        ];
    }
}
