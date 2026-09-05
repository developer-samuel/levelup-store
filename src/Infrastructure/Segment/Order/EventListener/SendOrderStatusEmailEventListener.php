<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Segment\Audit\Enum\AuditAction,
    Segment\Order\Event\OrderStatusChangedEvent
};

use App\Core\Ports\Segment\Audit\AuditLoggerContract;

use App\Infrastructure\Segment\Order\Email\OrderStatusEmail;

#[AsEventListener(event: OrderStatusChangedEvent::class)]
final readonly class SendOrderStatusEmailEventListener
{
    /**
     * @param OrderStatusEmail $orderStatusEmail
     * @param AuditLoggerContract $audit
    */
    public function __construct(
        private OrderStatusEmail $orderStatusEmail,
        private AuditLoggerContract $audit,
    ) {}

    /**
     * @param OrderStatusChangedEvent $event
     *
     * @return void
    */
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
        if (!$personal) {
            return;
        }

        $email = $personal->getEmail();
        if (!$email) {
            return;
        }

        $this->orderStatusEmail->send($email, $event->order);
    }
}
