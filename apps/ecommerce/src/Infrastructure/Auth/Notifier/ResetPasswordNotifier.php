<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Auth\Event\ResetPasswordCompletedEvent,
    Segment\User\Entity\User
};

use App\Core\Ports\Auth\Notifier\ResetPasswordNotifierContract;

final readonly class ResetPasswordNotifier implements ResetPasswordNotifierContract
{
    /**
     * @param EventDispatcherInterface $dispatcher
    */
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    /**
     * @param User $user
     *
     * @return void
    */
    public function send(User $user): void
    {
        $event = new ResetPasswordCompletedEvent($user);
        $this->dispatcher->dispatch($event);
    }
}
