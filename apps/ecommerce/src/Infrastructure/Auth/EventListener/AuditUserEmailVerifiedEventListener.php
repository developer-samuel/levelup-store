<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Auth\Event\UserEmailVerifiedEvent,
    Shared\Audit\Enum\AuditAction
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: UserEmailVerifiedEvent::class)]
final readonly class AuditUserEmailVerifiedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(UserEmailVerifiedEvent $event): void
    {
        $this->audit->log(
            AuditAction::EMAIL_VERIFIED,
            'User',
            $event->user->getId(),
            [],
            $event->user,
        );
    }
}
