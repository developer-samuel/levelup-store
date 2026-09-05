<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\Segment\Order\Event\OrderStatusUpdatedEvent;

use App\Core\Ports\Gateways\External\Realtime\MercureHubGatewayContract;

#[AsEventListener(event: OrderStatusUpdatedEvent::class)]
final readonly class PublishOrderStatusEventListener
{
    /**
     * @param MercureHubGatewayContract $mercureHubGateway
    */
    public function __construct(
        private MercureHubGatewayContract $mercureHubGateway,
    ) {}

    /**
     * @param OrderStatusUpdatedEvent $event
     *
     * @return void
    */
    public function __invoke(OrderStatusUpdatedEvent $event): void
    {
        $order = $event->order;
        $topic = 'orders/' . $order->getCode() . '/status';

        $this->mercureHubGateway->publish(
            $topic,
            (string) json_encode([
                'orderCode' => $order->getCode(),
                'status'    => $order->getStatus()->value,
            ], JSON_THROW_ON_ERROR),
        );
    }
}
