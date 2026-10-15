<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Auth\Event\UserEmailVerifiedEvent,
    Auth\Event\UserLoggedInEvent,
    Auth\Event\UserRegisteredEvent,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Notifier\UserNotifierContract;

final readonly class UserNotifier implements UserNotifierContract
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function notifyLoggedIn(User $user): void
    {
        $this->dispatcher->dispatch(new UserLoggedInEvent($user));
    }

    public function notifyRegistered(User $user): void
    {
        $this->dispatcher->dispatch(new UserRegisteredEvent($user));
    }

    public function notifyEmailVerified(User $user): void
    {
        $this->dispatcher->dispatch(new UserEmailVerifiedEvent($user));
    }
}
