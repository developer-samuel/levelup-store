<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\Segment\Order\{
    Entity\Order,
    Event\OrderCompletedEvent,
    Event\OrderPlacedEvent,
    Event\OrderRefundedEvent
};

use App\Core\Ports\Segment\Order\Notifier\OrderNotifierContract;

final readonly class OrderNotifier implements OrderNotifierContract
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function notifyPlaced(Order $order): void
    {
        $this->dispatcher->dispatch(new OrderPlacedEvent($order));
    }

    public function notifyCompleted(Order $order): void
    {
        $this->dispatcher->dispatch(new OrderCompletedEvent($order));
    }

    public function notifyRefunded(Order $order): void
    {
        $this->dispatcher->dispatch(new OrderRefundedEvent($order));
    }
}
