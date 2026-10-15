<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Order\Event\OrderCompletedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: OrderCompletedEvent::class)]
final readonly class AuditOrderCompletedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(OrderCompletedEvent $event): void
    {
        $this->audit->log(
            AuditAction::ORDER_COMPLETED,
            'Order',
            $event->order->getId(),
            ['code' => $event->order->getCode()],
            $event->order->getUser(),
        );
    }
}
