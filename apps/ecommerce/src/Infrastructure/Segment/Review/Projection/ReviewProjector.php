<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Projection;

use App\Core\Domain\Segment\Review\Entity\Review;

use App\Core\Ports\{
    Gateways\External\Search\ElasticsearchGatewayContract,
    Segment\Review\Repository\ReviewRepositoryContract,
    Shared\ReindexableInterface
};

final readonly class ReviewProjector implements ReindexableInterface
{
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
        private ReviewRepositoryContract $reviewRepository,
    ) {}

    public function index(Review $review): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->indexDocument(ReviewProjection::NAME, $review->getId(), $this->buildDocument($review));
    }

    public function remove(Review $review): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(ReviewProjection::NAME, $review->getId());
    }

    public function reindexAll(): int
    {
        $this->elasticsearch->ensureIndexExists(ReviewProjection::NAME, ReviewProjection::mapping());

        $reviews = $this->reviewRepository->findAll();
        $indexed = 0;

        foreach ($reviews as $review) {
            $this->index($review);
            ++$indexed;
        }

        return $indexed;
    }

    public function getIndexName(): string
    {
        return ReviewProjection::NAME;
    }

    /** @return array<string, mixed> */
    private function buildDocument(Review $review): array
    {
        return [
            'body'       => $review->getBody(),
            'value'      => $review->getValue(),
            'type'       => $review->getType()->value,
            'user_id'    => $review->getUser()->getId(),
            'variant_id' => $review->getVariant()->getId(),
            'created_at' => $review->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
