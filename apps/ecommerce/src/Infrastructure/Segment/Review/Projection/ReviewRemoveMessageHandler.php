<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Review\Message\ReviewRemoveMessage;

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;


#[AsMessageHandler]
final readonly class ReviewRemoveMessageHandler
{
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
    ) {}

    public function __invoke(ReviewRemoveMessage $message): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(ReviewProjection::NAME, $message->reviewId);
    }
}
