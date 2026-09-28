<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Event\OrderStatusChangedEvent
};

use App\Core\Ports\Segment\Order\Notifier\OrderStatusNotifierContract;

final readonly class OrderStatusNotifier implements OrderStatusNotifierContract
{
    /**
     * @param EventDispatcherInterface $dispatcher
    */
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    /**
     * @param Order $order
     *
     * @return void
    */
    public function send(Order $order): void
    {
        $event = new OrderStatusChangedEvent($order);
        $this->dispatcher->dispatch($event);
    }
}
