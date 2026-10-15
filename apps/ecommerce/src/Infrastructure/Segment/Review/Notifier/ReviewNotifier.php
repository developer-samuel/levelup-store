<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Segment\Review\Event\ReviewDestroyedEvent,
    Segment\Review\Event\ReviewSubmittedEvent,
    Segment\User\Entity\User
};

use App\Core\Ports\Segment\Review\Notifier\ReviewNotifierContract;

final readonly class ReviewNotifier implements ReviewNotifierContract
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function notifySubmitted(int $variantId, User $user): void
    {
        $this->dispatcher->dispatch(new ReviewSubmittedEvent($variantId, $user));
    }

    public function notifyDestroyed(int $reviewId, User $user): void
    {
        $this->dispatcher->dispatch(new ReviewDestroyedEvent($reviewId, $user));
    }
}
