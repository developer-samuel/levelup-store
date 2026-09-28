<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Product\Message\ProductVariantIndexMessage;

use App\Core\Ports\Segment\Product\Repository\Variant\ProductVariantRepositoryContract;

#[AsMessageHandler]
final readonly class ProductVariantIndexMessageHandler
{
    /**
     * @param ProductVariantRepositoryContract $variantRepository
     * @param ProductVariantProjector $projector
    */
    public function __construct(
        private ProductVariantRepositoryContract $variantRepository,
        private ProductVariantProjector $projector,
    ) {}

    /**
     * @param ProductVariantIndexMessage $message
     *
     * @return void
    */
    public function __invoke(ProductVariantIndexMessage $message): void
    {
        $variant = $this->variantRepository->findById($message->variantId);

        if ($variant === null) {
            return;
        }

        $this->projector->index($variant);
    }
}
