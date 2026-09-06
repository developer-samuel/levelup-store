<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Segment\Audit\Enum\AuditAction,
    Segment\Order\Event\OrderConfirmationRequestedEvent
};

use App\Core\Ports\Segment\Audit\AuditLoggerContract;

use App\Infrastructure\Segment\Order\Email\OrderConfirmationEmail;

#[AsEventListener(event: OrderConfirmationRequestedEvent::class)]
final readonly class SendOrderConfirmationEmailEventListener
{
    /**
     * @param OrderConfirmationEmail $orderConfirmationEmail
     * @param AuditLoggerContract $audit
    */
    public function __construct(
        private OrderConfirmationEmail $orderConfirmationEmail,
        private AuditLoggerContract $audit,
    ) {}

    /**
     * @param OrderConfirmationRequestedEvent $event
     *
     * @return void
    */
    public function __invoke(OrderConfirmationRequestedEvent $event): void
    {
        $this->audit->log(
            AuditAction::ORDER_CREATED,
            'Order',
            $event->order->getId(),
            [],
            $event->order->getUser(),
        );

        $this->orderConfirmationEmail->send(
            $event->personal->getEmail(),
            $event->order,
            $event->personal,
            $event->billing,
            $event->shipping,
            $event->items,
        );
    }
}
