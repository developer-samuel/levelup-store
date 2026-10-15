<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Order\Event\OrderPlacedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: OrderPlacedEvent::class)]
final readonly class AuditOrderPlacedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(OrderPlacedEvent $event): void
    {
        $this->audit->log(
            AuditAction::ORDER_PLACED,
            'Order',
            $event->order->getId(),
            ['code' => $event->order->getCode()],
            $event->order->getUser(),
        );
    }
}
