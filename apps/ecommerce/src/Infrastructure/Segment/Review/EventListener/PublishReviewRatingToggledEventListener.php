<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use App\Core\Domain\Segment\Review\Event\ReviewRatingToggledEvent;

use App\Core\Ports\{
    Gateways\External\Realtime\MercureHubGatewayContract,
    Segment\Review\Repository\ReviewRatingRepositoryContract
};

#[AsEventListener(event: ReviewRatingToggledEvent::class)]
final readonly class PublishReviewRatingToggledEventListener
{
    public function __construct(
        private MercureHubGatewayContract $mercureHubGateway,
        private ReviewRatingRepositoryContract $reviewRatingRepository,
    ) {}

    public function __invoke(ReviewRatingToggledEvent $event): void
    {
        $likesCount = $this->reviewRatingRepository->countByType($event->reviewId, 'like');
        $dislikesCount = $this->reviewRatingRepository->countByType($event->reviewId, 'dislike');

        $this->mercureHubGateway->publish(
            sprintf('reviews/%d/ratings', $event->variantId),
            json_encode([
                'reviewId'      => $event->reviewId,
                'likesCount'    => $likesCount,
                'dislikesCount' => $dislikesCount,
            ], JSON_THROW_ON_ERROR),
        );
    }
}
