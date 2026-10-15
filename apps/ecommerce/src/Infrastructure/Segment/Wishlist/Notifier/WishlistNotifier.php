<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Wishlist\Notifier;

use Psr\EventDispatcher\EventDispatcherInterface;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\Wishlist\Event\WishlistItemAddedEvent,
    Segment\Wishlist\Event\WishlistItemRemovedEvent
};

use App\Core\Ports\Segment\Wishlist\Notifier\WishlistNotifierContract;

final readonly class WishlistNotifier implements WishlistNotifierContract
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function notifyAdded(User $user, int $variantId): void
    {
        $this->dispatcher->dispatch(new WishlistItemAddedEvent($user, $variantId));
    }

    public function notifyRemoved(User $user, int $variantId): void
    {
        $this->dispatcher->dispatch(new WishlistItemRemovedEvent($user, $variantId));
    }
}
