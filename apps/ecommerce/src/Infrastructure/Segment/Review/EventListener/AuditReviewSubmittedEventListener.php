<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\{
    Shared\Audit\Enum\AuditAction,
    Segment\Review\Event\ReviewSubmittedEvent
};

use App\Core\Ports\Shared\Audit\AuditLoggerContract;

#[AsEventListener(event: ReviewSubmittedEvent::class)]
final readonly class AuditReviewSubmittedEventListener
{
    public function __construct(
        private AuditLoggerContract $audit,
    ) {}

    public function __invoke(ReviewSubmittedEvent $event): void
    {
        $this->audit->log(
            AuditAction::REVIEW_SUBMITTED,
            'ProductVariant',
            $event->variantId,
            [],
            $event->user,
        );
    }
}
