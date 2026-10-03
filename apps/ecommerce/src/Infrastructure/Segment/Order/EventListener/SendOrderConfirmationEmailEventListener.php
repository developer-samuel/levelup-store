<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Order\Event\OrderConfirmationRequestedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

use App\Infrastructure\Segment\Order\Email\OrderConfirmationEmail;

#[AsEventListener(event: OrderConfirmationRequestedEvent::class)]
final readonly class SendOrderConfirmationEmailEventListener
{
    public function __construct(
        private OrderConfirmationEmail $orderConfirmationEmail,
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(OrderConfirmationRequestedEvent $event): void
    {
        $this->audit->log(
            AuditAction::ORDER_CREATED,
            'Order',
            $event->order->getId(),
            [],
            $event->order->getUser(),
        );

        $email = $event->personal->getEmail();

        if (str_ends_with($email, '@example.com')) {
            return;
        }

        $this->orderConfirmationEmail->send(
            $email,
            $event->order,
            $event->personal,
            $event->billing,
            $event->shipping,
            $event->items,
        );
    }
}
