<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Auth\Event\UserLoggedInEvent,
    Shared\Audit\Enum\AuditAction
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: UserLoggedInEvent::class)]
final readonly class AuditUserLoggedInEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(UserLoggedInEvent $event): void
    {
        $this->audit->log(
            AuditAction::LOGIN,
            'User',
            $event->user->getId(),
            [],
            $event->user,
        );
    }
}
