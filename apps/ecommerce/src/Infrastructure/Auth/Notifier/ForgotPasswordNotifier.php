<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Core\Domain\{
    Auth\Event\ForgotPasswordRequestedEvent,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Notifier\ForgotPasswordNotifierContract;

final readonly class ForgotPasswordNotifier implements ForgotPasswordNotifierContract
{
    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param EventDispatcherInterface $dispatcher
    */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EventDispatcherInterface $dispatcher,
    ) {}

    /**
     * @param User $user
     * @param string $token
     *
     * @return void
    */
    public function send(User $user, string $token): void
    {
        $resetUrl = $this->urlGenerator->generate(
            'reset_password',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $event = new ForgotPasswordRequestedEvent($user, $resetUrl);
        $this->dispatcher->dispatch($event);
    }
}
