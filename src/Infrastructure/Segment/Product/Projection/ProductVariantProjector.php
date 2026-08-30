<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

use Kit\Utils\Shared\Normalizer\StringNormalizer;

use App\Core\Ports\{
    Gateways\External\Search\ElasticsearchGatewayContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Review\Repository\ReviewRepositoryContract,
    Shared\ReindexableInterface
};

final readonly class ProductVariantProjector implements ReindexableInterface
{
    /**
     * @param ElasticsearchGatewayContract $elasticsearch
     * @param ProductVariantRepositoryContract $variantRepository
     * @param ReviewRepositoryContract $reviewRepository
    */
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
        private ProductVariantRepositoryContract $variantRepository,
        private ReviewRepositoryContract $reviewRepository,
    ) {}

    /**
     * @param ProductVariant $variant
     * @param float $avgRating
     *
     * @return void
    */
    public function index(ProductVariant $variant, float $avgRating = 0.0): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->indexDocument(ProductVariantProjection::NAME, $variant->getId(), $this->buildDocument($variant, $avgRating));
    }

    /**
     * @param ProductVariant $variant
     *
     * @return void
    */
    public function remove(ProductVariant $variant): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(ProductVariantProjection::NAME, $variant->getId());
    }

    /**
     * @return int
    */
    public function reindexAll(): int
    {
        $this->elasticsearch->ensureIndexExists(ProductVariantProjection::NAME, ProductVariantProjection::mapping());

        $variants = $this->variantRepository->findAll();

        $variantIds = array_values(array_map(static fn(ProductVariant $v): int => $v->getId(), $variants));
        $ratings    = $this->reviewRepository->getAverageRatingsByVariantIds($variantIds);

        $indexed = 0;

        foreach ($variants as $variant) {
            $this->index($variant, $ratings[$variant->getId()] ?? 0.0);
            ++$indexed;
        }

        return $indexed;
    }

    /**
     * @return string
    */
    public function getIndexName(): string
    {
        return ProductVariantProjection::NAME;
    }

    /**
     * @param ProductVariant $variant
     * @param float $avgRating
     *
     * @return array<string, mixed>
    */
    private function buildDocument(ProductVariant $variant, float $avgRating): array
    {
        $product  = $variant->getProduct();
        $discount = $variant->getDiscount();
        $stock    = $variant->getStock();

        $subtypes = [];
        foreach ($product->getSubtypes() as $productSubtype) {
            $subtypes[] = StringNormalizer::normalize($productSubtype->getSubtype()->getName());
        }

        return [
            'name'            => $variant->getName(),
            'effective_price' => round($variant->getPrice() - ($discount?->getPrice() ?? 0.0), 2),
            'has_discount'    => $discount !== null,
            'is_available'    => $stock !== null && $stock->isAvailable(),
            'brand'           => StringNormalizer::normalize($product->getBrand()->getName()),
            'category'        => StringNormalizer::normalize($product->getCategory()->getName()),
            'type'            => StringNormalizer::normalize($product->getType()->getName()),
            'subtypes'        => $subtypes,
            'avg_rating'      => $avgRating,
            'created_at'      => $variant->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
