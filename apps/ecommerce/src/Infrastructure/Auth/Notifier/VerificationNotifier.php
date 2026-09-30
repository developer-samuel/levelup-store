<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Core\Domain\{
    Auth\Event\VerificationRequestedEvent,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Notifier\VerificationNotifierContract;

final readonly class VerificationNotifier implements VerificationNotifierContract
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
        $url = $this->urlGenerator->generate(
            'verification_update',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $event = new VerificationRequestedEvent($user, $url);
        $this->dispatcher->dispatch($event);
    }
}
