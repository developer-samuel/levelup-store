<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Order\Event\OrderRefundedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: OrderRefundedEvent::class)]
final readonly class AuditOrderRefundedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(OrderRefundedEvent $event): void
    {
        $this->audit->log(
            AuditAction::ORDER_REFUNDED,
            'Order',
            $event->order->getId(),
            ['code' => $event->order->getCode()],
            $event->order->getUser(),
        );
    }
}
