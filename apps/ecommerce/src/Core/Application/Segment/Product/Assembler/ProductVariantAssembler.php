<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Assembler;

use Presets\Kit\Assertion\Shared\IdAssertion;

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
 *     discountPrice: float|null
 * }
 */
final readonly class ProductVariantAssembler implements ProductVariantAssemblerContract
{
    public function __construct(
        private ProductVariantFactory $variantFactory,
    ) {}

    public function toObject(
        ProductVariant $variant,
        ReviewQueryContract $reviewQuery,
    ): ProductVariantObject {
        $variantId = IdAssertion::assert($variant->getId(), 'Variant ID', \LogicException::class);

        $prices = self::normalizePrices(ProductVariantResource::extractPrices($variant));
        $averageRating = $reviewQuery->getAverageRatingByVariant($variantId);

        return $this->createProductVariantObject($variant, $prices, $averageRating);
    }

    /** @param array{price: float, discount: float|null, discountPrice: float|null} $prices */
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
     * @return array{price: float, discount: float|null, discountPrice: float|null}
     */
    private function normalizePrices(array $prices): array
    {
        return [
            'price'         => (float) $prices['price'],
            'discount'      => $prices['discount'] !== null ? (float) $prices['discount'] : null,
            'discountPrice' => $prices['discountPrice'] !== null ? (float) $prices['discountPrice'] : null,
        ];
    }
}
