<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Segment\Product\EventListener;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\Segment\Product\Event\ProductStockUpdatedEvent;

use App\Core\Ports\Gateways\External\Realtime\MercureHubGatewayContract;

use App\Infrastructure\Segment\Product\EventListener\PublishProductStockUpdatedEventListener;

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Product\EventListener\PublishProductStockUpdatedEventListener
*/
class PublishProductStockUpdatedEventListenerTest extends TestCase
{
    private MercureHubGatewayContract&MockObject $mercureHubGateway;
    private PublishProductStockUpdatedEventListener $listener;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initListener();
    }

    public function testPublishesOnProductStockUpdated(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish');

        ($this->listener)(new ProductStockUpdatedEvent(1, 10, true));
    }

    public function testPublishesCorrectTopic(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with('products/42/stock', $this->anything());

        ($this->listener)(new ProductStockUpdatedEvent(42, 10, true));
    }

    public function testPublishesCorrectPayload(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->anything(),
                json_encode([
                    'variantId'         => 42,
                    'quantityAvailable' => 10,
                    'inStock'           => true,
                ]),
            );

        ($this->listener)(new ProductStockUpdatedEvent(42, 10, true));
    }

    public function testPublishesInStockFalseWhenOutOfStock(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->anything(),
                json_encode([
                    'variantId'         => 42,
                    'quantityAvailable' => 0,
                    'inStock'           => false,
                ]),
            );

        ($this->listener)(new ProductStockUpdatedEvent(42, 0, false));
    }

    private function initMocks(): void
    {
        $this->mercureHubGateway = $this->createMock(MercureHubGatewayContract::class);
    }

    private function initListener(): void
    {
        $this->listener = new PublishProductStockUpdatedEventListener($this->mercureHubGateway);
    }
}
