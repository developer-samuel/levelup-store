<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Segment\Order\EventListener;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderStatus,
    Segment\Order\Event\OrderStatusUpdatedEvent
};

use App\Core\Ports\Gateways\External\Realtime\MercureHubGatewayContract;

use App\Infrastructure\Segment\Order\EventListener\PublishOrderStatusEventListener;

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Order\EventListener\PublishOrderStatusEventListener
*/
class PublishOrderStatusEventListenerTest extends TestCase
{
    private MercureHubGatewayContract&MockObject $mercureHubGateway;
    private PublishOrderStatusEventListener $listener;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initListener();
    }

    public function testPublishesOnOrderStatusChanged(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish');

        ($this->listener)(new OrderStatusUpdatedEvent($this->buildOrder()));
    }

    public function testPublishesCorrectTopic(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with('orders/ORD-123/status', $this->anything());

        ($this->listener)(new OrderStatusUpdatedEvent($this->buildOrder()));
    }

    public function testPublishesCorrectPayload(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->anything(),
                json_encode([
                    'orderCode' => 'ORD-123',
                    'status'    => 'shipped',
                ]),
            );

        ($this->listener)(new OrderStatusUpdatedEvent($this->buildOrder()));
    }

    private function initMocks(): void
    {
        $this->mercureHubGateway = $this->createMock(MercureHubGatewayContract::class);
    }

    private function initListener(): void
    {
        $this->listener = new PublishOrderStatusEventListener($this->mercureHubGateway);
    }

    private function buildOrder(): Order
    {
        $order = $this->createMock(Order::class);
        $order->method('getCode')->willReturn('ORD-123');
        $order->method('getStatus')->willReturn(OrderStatus::SHIPPED);

        return $order;
    }
}
