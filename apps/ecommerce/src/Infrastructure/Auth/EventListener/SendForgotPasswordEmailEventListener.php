<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\Auth\Event\ForgotPasswordRequestedEvent;

use App\Infrastructure\Auth\Email\ForgotPasswordEmail;

#[AsEventListener(event: ForgotPasswordRequestedEvent::class)]
final readonly class SendForgotPasswordEmailEventListener
{
    /**
     * @param ForgotPasswordEmail $forgotPasswordEmail
    */
    public function __construct(
        private ForgotPasswordEmail $forgotPasswordEmail,
    ) {}

    /**
     * @param ForgotPasswordRequestedEvent $event
     *
     * @return void
    */
    public function __invoke(ForgotPasswordRequestedEvent $event): void
    {
        $this->forgotPasswordEmail->send(
            $event->user->getEmail(),
            $event->resetUrl,
            $event->user,
        );
    }
}
