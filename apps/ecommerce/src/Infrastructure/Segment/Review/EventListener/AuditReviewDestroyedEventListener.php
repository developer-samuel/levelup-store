<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Review\Event\ReviewDestroyedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: ReviewDestroyedEvent::class)]
final readonly class AuditReviewDestroyedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(ReviewDestroyedEvent $event): void
    {
        $this->audit->log(
            AuditAction::REVIEW_DESTROYED,
            'Review',
            $event->reviewId,
            [],
            $event->user,
        );
    }
}
