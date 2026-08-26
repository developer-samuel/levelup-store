<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Order\Message\OrderRemoveMessage;

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;

use App\Infrastructure\Segment\Order\Projection\OrderProjection;

#[AsMessageHandler]
final readonly class OrderRemoveMessageHandler
{
    /**
     * @param ElasticsearchGatewayContract $elasticsearch
    */
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
    ) {}

    /**
     * @param OrderRemoveMessage $message
     *
     * @return void
    */
    public function __invoke(OrderRemoveMessage $message): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(OrderProjection::NAME, $message->orderId);
    }
}
