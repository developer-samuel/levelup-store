<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Wishlist\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Wishlist\Event\WishlistItemRemovedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: WishlistItemRemovedEvent::class)]
final readonly class AuditWishlistItemRemovedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(WishlistItemRemovedEvent $event): void
    {
        $this->audit->log(
            AuditAction::WISHLIST_ITEM_REMOVED,
            'ProductVariant',
            $event->variantId,
            [],
            $event->user,
        );
    }
}
