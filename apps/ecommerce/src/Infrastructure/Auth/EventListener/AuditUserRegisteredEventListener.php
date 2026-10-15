<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Auth\Event\UserRegisteredEvent,
    Shared\Audit\Enum\AuditAction
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: UserRegisteredEvent::class)]
final readonly class AuditUserRegisteredEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(UserRegisteredEvent $event): void
    {
        $this->audit->log(
            AuditAction::SIGNUP,
            'User',
            $event->user->getId(),
            [],
            $event->user,
        );
    }
}
