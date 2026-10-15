<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\Segment\User\{
    Entity\User,
    Event\UserPasswordChangedEvent,
    Event\UserProfileDestroyedEvent,
    Event\UserProfileUpdatedEvent
};

use App\Core\Ports\Segment\User\Notifier\UserNotifierContract;

final readonly class UserNotifier implements UserNotifierContract
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function notifyPasswordChanged(User $user): void
    {
        $this->dispatcher->dispatch(new UserPasswordChangedEvent($user));
    }

    public function notifyProfileDestroyed(User $user): void
    {
        $this->dispatcher->dispatch(new UserProfileDestroyedEvent($user));
    }

    public function notifyProfileUpdated(User $user): void
    {
        $this->dispatcher->dispatch(new UserProfileUpdatedEvent($user));
    }
}
