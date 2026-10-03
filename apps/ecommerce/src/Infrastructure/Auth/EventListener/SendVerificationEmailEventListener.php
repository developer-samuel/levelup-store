<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\Auth\Event\VerificationRequestedEvent;

use App\Infrastructure\Auth\Email\VerificationEmail;

#[AsEventListener(event: VerificationRequestedEvent::class)]
final readonly class SendVerificationEmailEventListener
{
    public function __construct(
        private VerificationEmail $verificationEmail,
    ) {}

    public function __invoke(VerificationRequestedEvent $event): void
    {
        $this->verificationEmail->send(
            $event->user->getEmail(),
            $event->verificationUrl,
            $event->user,
        );
    }
}
