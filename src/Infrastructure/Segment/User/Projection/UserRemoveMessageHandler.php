<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\User\Message\UserRemoveMessage;

use App\Core\Ports\Gateways\External\Search\ElasticsearchGatewayContract;

use App\Infrastructure\Segment\User\Projection\UserProjection;

#[AsMessageHandler]
final readonly class UserRemoveMessageHandler
{
    /**
     * @param ElasticsearchGatewayContract $elasticsearch
    */
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
    ) {}

    /**
     * @param UserRemoveMessage $message
     *
     * @return void
    */
    public function __invoke(UserRemoveMessage $message): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(UserProjection::NAME, $message->userId);
    }
}
