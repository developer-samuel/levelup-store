<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Wishlist\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Wishlist\Event\WishlistItemAddedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: WishlistItemAddedEvent::class)]
final readonly class AuditWishlistItemAddedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(WishlistItemAddedEvent $event): void
    {
        $this->audit->log(
            AuditAction::WISHLIST_ITEM_ADDED,
            'ProductVariant',
            $event->variantId,
            [],
            $event->user,
        );
    }
}
