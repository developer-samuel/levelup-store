<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Product\Message\ProductVariantRemoveMessage;

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;

#[AsMessageHandler]
final readonly class ProductVariantRemoveMessageHandler
{
    /**
     * @param ElasticsearchGatewayContract $elasticsearch
    */
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
    ) {}

    /**
     * @param ProductVariantRemoveMessage $message
     *
     * @return void
    */
    public function __invoke(ProductVariantRemoveMessage $message): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(ProductVariantProjection::NAME, $message->variantId);
    }
}
