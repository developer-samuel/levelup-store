<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Segment\User\Event\UserPasswordChangedEvent,
    Shared\Audit\Enum\AuditAction
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: UserPasswordChangedEvent::class)]
final readonly class AuditUserPasswordChangedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(UserPasswordChangedEvent $event): void
    {
        $this->audit->log(
            AuditAction::PASSWORD_CHANGE,
            'User',
            $event->user->getId(),
            [],
            $event->user,
        );
    }
}
