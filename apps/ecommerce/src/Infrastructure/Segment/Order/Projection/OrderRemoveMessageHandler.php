<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Order\Message\OrderRemoveMessage;

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;


#[AsMessageHandler]
final readonly class OrderRemoveMessageHandler
{
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
    ) {}

    public function __invoke(OrderRemoveMessage $message): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(OrderProjection::NAME, $message->orderId);
    }
}
