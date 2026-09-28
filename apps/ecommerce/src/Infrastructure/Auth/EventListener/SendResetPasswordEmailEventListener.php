<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Auth\Event\ResetPasswordCompletedEvent,
    Segment\Audit\Enum\AuditAction
};

use App\Core\Ports\Segment\Audit\AuditLoggerContract;

use App\Infrastructure\Auth\Email\ResetPasswordEmail;

#[AsEventListener(event: ResetPasswordCompletedEvent::class)]
final readonly class SendResetPasswordEmailEventListener
{
    /**
     * @param ResetPasswordEmail $resetPasswordEmail
     * @param AuditLoggerContract $audit
    */
    public function __construct(
        private ResetPasswordEmail $resetPasswordEmail,
        private AuditLoggerContract $audit,
    ) {}

    /**
     * @param ResetPasswordCompletedEvent $event
     *
     * @return void
    */
    public function __invoke(ResetPasswordCompletedEvent $event): void
    {
        $this->audit->log(
            AuditAction::PASSWORD_RESET,
            'User',
            $event->user->getId(),
        );

        $this->resetPasswordEmail->send(
            $event->user->getEmail(),
            $event->user,
        );
    }
}
