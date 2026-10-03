<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Order\Event\OrderStatusChangedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

use App\Infrastructure\Segment\Order\Email\OrderStatusEmail;

#[AsEventListener(event: OrderStatusChangedEvent::class)]
final readonly class SendOrderStatusEmailEventListener
{
    public function __construct(
        private OrderStatusEmail $orderStatusEmail,
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(OrderStatusChangedEvent $event): void
    {
        $this->audit->log(
            AuditAction::ORDER_STATUS_CHANGE,
            'Order',
            $event->order->getId(),
            ['status' => $event->order->getStatus()->value],
            $event->order->getUser(),
        );

        $personal = $event->order->getPersonal();
        if ($personal === null) {
            return;
        }

        $email = $personal->getEmail();
        if ($email === '') {
            return;
        }

        $this->orderStatusEmail->send($email, $event->order);
    }
}
