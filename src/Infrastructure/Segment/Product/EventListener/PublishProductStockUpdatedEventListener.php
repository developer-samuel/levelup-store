<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\Segment\Product\Event\ProductStockUpdatedEvent;

use App\Core\Ports\Gateways\External\Realtime\MercureHubGatewayContract;

#[AsEventListener(event: ProductStockUpdatedEvent::class)]
final readonly class PublishProductStockUpdatedEventListener
{
    /**
     * @param MercureHubGatewayContract $mercureHubGateway
    */
    public function __construct(
        private MercureHubGatewayContract $mercureHubGateway,
    ) {}

    /**
     * @param ProductStockUpdatedEvent $event
     *
     * @return void
    */
    public function __invoke(ProductStockUpdatedEvent $event): void
    {
        $this->mercureHubGateway->publish(
            "products/{$event->variantId}/stock",
            (string) json_encode([
                'variantId'         => $event->variantId,
                'quantityAvailable' => $event->quantityAvailable,
                'inStock'           => $event->inStock,
            ], JSON_THROW_ON_ERROR),
        );
    }
}
