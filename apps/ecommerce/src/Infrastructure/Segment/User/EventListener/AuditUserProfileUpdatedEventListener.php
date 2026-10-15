<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Segment\User\Event\UserProfileUpdatedEvent,
    Shared\Audit\Enum\AuditAction
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: UserProfileUpdatedEvent::class)]
final readonly class AuditUserProfileUpdatedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(UserProfileUpdatedEvent $event): void
    {
        $this->audit->log(
            AuditAction::PROFILE_UPDATE,
            'User',
            $event->user->getId(),
            [],
            $event->user,
        );
    }
}
