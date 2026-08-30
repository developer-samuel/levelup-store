<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Handler\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Utils\ProductToolkit,
    Segment\Product\ValueObject\ProductDetailObject
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Handler\Query\ProductDetailQueryHandlerContract,
    Segment\Product\Service\Query\ProductDescriptionQueryContract,
    Segment\Product\Service\Query\ProductPriceQueryContract,
    Segment\Product\Service\Query\ProductVariantQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract,
    Segment\Wishlist\Service\Query\WishlistQueryContract
};

final readonly class ProductDetailQueryHandler implements ProductDetailQueryHandlerContract
{
    /**
     * @param SecurityProviderContract $securityProvider
     * @param ProductDescriptionQueryContract $productDescriptionQuery
     * @param ProductVariantQueryContract $productVariantQuery
     * @param ProductPriceQueryContract $productPriceQuery
     * @param ReviewQueryContract $reviewQuery
     * @param WishlistQueryContract $wishlistQuery
    */
    public function __construct(
        private SecurityProviderContract $securityProvider,
        private ProductDescriptionQueryContract $productDescriptionQuery,
        private ProductPriceQueryContract $productPriceQuery,
        private ProductVariantQueryContract $productVariantQuery,
        private ReviewQueryContract $reviewQuery,
        private WishlistQueryContract $wishlistQuery,
    ) {}

    /**
     * @param string $url
     *
     * @return ProductDetailObject|null
    */
    public function handle(string $url): ?ProductDetailObject
    {
        $variant = $this->productVariantQuery->getVariantOrNull($url);
        if (!$variant) {
            return null;
        }

        $variants = $this->productVariantQuery->getAllVariantsOrNull($variant);
        if (empty($variants)) {
            return null;
        }

        $stock = $variant->getInStock();
        if ($stock === null) {
            return null;
        }
        
        return $this->createFormattedDetail($variant, $variants, $stock);
    }

    /**
     * @param ProductVariant $variant
     * @param ProductVariant[] $variants
     * @param ProductVariantStock $stock
     *
     * @return ProductDetailObject
    */
    private function createFormattedDetail(
        ProductVariant $variant,
        array $variants,
        ProductVariantStock $stock,
    ): ProductDetailObject {
        $currentUser = $this->securityProvider->getCurrentUser();

        return new ProductDetailObject(
            variant: $variant,
            variants: $variants,
            stocks: $stock,
            price: $this->productPriceQuery->getPrice($variant),
            descriptions: $this->productDescriptionQuery->getProductDescriptions($variant),
            wishlistExists: $this->wishlistQuery->inCurrentUserWishlist($variant),
            firstImage: ProductToolkit::getFirstImagePath($variant),
            reviewData: $this->reviewQuery->getLastReviewData($variant, $currentUser),
        );
    }
}
