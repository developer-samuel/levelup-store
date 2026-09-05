<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Segment\Review\EventListener;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\Segment\Review\Event\ReviewRatingToggledEvent;

use App\Core\Ports\{
    Gateways\External\Realtime\MercureHubGatewayContract,
    Segment\Review\Repository\ReviewRatingRepositoryContract
};

use App\Infrastructure\Segment\Review\EventListener\PublishReviewRatingToggledEventListener;

/**
 * @coversDefaultClass \App\Infrastructure\Segment\Review\EventListener\PublishReviewRatingToggledEventListener
*/
class PublishReviewRatingToggledEventListenerTest extends TestCase
{
    private MercureHubGatewayContract&MockObject $mercureHubGateway;
    private ReviewRatingRepositoryContract&MockObject $reviewRatingRepository;
    private PublishReviewRatingToggledEventListener $listener;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initListener();
    }

    public function testPublishesOnReviewRatingToggled(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish');

        ($this->listener)(new ReviewRatingToggledEvent(variantId: 5, reviewId: 10));
    }

    public function testPublishesCorrectTopic(): void
    {
        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with('reviews/5/ratings', $this->anything());

        ($this->listener)(new ReviewRatingToggledEvent(variantId: 5, reviewId: 10));
    }

    public function testPublishesCorrectPayload(): void
    {
        $this->reviewRatingRepository->method('countByType')->willReturnMap([
            [10, 'like',    3],
            [10, 'dislike', 1],
        ]);

        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->anything(),
                json_encode([
                    'reviewId'      => 10,
                    'likesCount'    => 3,
                    'dislikesCount' => 1,
                ]),
            );

        ($this->listener)(new ReviewRatingToggledEvent(variantId: 5, reviewId: 10));
    }

    public function testFetchesLikesCountFromRepository(): void
    {
        $this->reviewRatingRepository
            ->method('countByType')
            ->willReturnMap([
                [10, 'like',    7],
                [10, 'dislike', 0],
            ]);

        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with($this->anything(), $this->stringContains('"likesCount":7'));

        ($this->listener)(new ReviewRatingToggledEvent(variantId: 5, reviewId: 10));
    }

    public function testFetchesDislikesCountFromRepository(): void
    {
        $this->reviewRatingRepository
            ->method('countByType')
            ->willReturnMap([
                [10, 'like',    0],
                [10, 'dislike', 4],
            ]);

        $this->mercureHubGateway
            ->expects($this->once())
            ->method('publish')
            ->with($this->anything(), $this->stringContains('"dislikesCount":4'));

        ($this->listener)(new ReviewRatingToggledEvent(variantId: 5, reviewId: 10));
    }

    private function initMocks(): void
    {
        $this->mercureHubGateway      = $this->createMock(MercureHubGatewayContract::class);
        $this->reviewRatingRepository = $this->createMock(ReviewRatingRepositoryContract::class);
    }

    private function initListener(): void
    {
        $this->listener = new PublishReviewRatingToggledEventListener(
            $this->mercureHubGateway,
            $this->reviewRatingRepository,
        );
    }
}
