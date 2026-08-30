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
    /**
     * @param MercureHubGatewayContract $mercureHubGateway
     * @param ReviewRatingRepositoryContract $reviewRatingRepository
    */
    public function __construct(
        private MercureHubGatewayContract $mercureHubGateway,
        private ReviewRatingRepositoryContract $reviewRatingRepository,
    ) {}

    /**
     * @param ReviewRatingToggledEvent $event
     *
     * @return void
    */
    public function __invoke(ReviewRatingToggledEvent $event): void
    {
        $likesCount    = $this->reviewRatingRepository->countByType($event->reviewId, 'like');
        $dislikesCount = $this->reviewRatingRepository->countByType($event->reviewId, 'dislike');

        $this->mercureHubGateway->publish(
            "reviews/{$event->variantId}/ratings",
            (string) json_encode([
                'reviewId'      => $event->reviewId,
                'likesCount'    => $likesCount,
                'dislikesCount' => $dislikesCount,
            ], JSON_THROW_ON_ERROR),
        );
    }
}
