<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Product\Message\ProductVariantIndexMessage;

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

#[AsMessageHandler]
final readonly class ProductVariantIndexMessageHandler
{
    public function __construct(
        private ProductVariantRepositoryContract $variantRepository,
        private ProductVariantProjector $projector,
        private ReviewQueryContract $reviewQuery,
    ) {}

    public function __invoke(ProductVariantIndexMessage $message): void
    {
        $variant = $this->variantRepository->findById($message->variantId);

        if ($variant === null) {
            return;
        }

        $avgRating = $this->reviewQuery->getAverageRatingByVariant($message->variantId);

        $this->projector->index($variant, $avgRating);
    }
}
