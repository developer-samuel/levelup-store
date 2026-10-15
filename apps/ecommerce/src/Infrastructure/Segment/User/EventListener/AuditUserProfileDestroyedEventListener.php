<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Segment\User\Event\UserProfileDestroyedEvent,
    Shared\Audit\Enum\AuditAction
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: UserProfileDestroyedEvent::class)]
final readonly class AuditUserProfileDestroyedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(UserProfileDestroyedEvent $event): void
    {
        $this->audit->log(
            AuditAction::PROFILE_DESTROY,
            'User',
            $event->user->getId(),
            [],
            $event->user,
        );
    }
}
