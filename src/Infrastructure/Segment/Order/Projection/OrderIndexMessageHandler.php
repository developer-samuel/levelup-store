<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Order\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Order\Message\OrderIndexMessage;

use App\Core\Ports\Segment\Order\Repository\OrderRepositoryContract;

#[AsMessageHandler]
final readonly class OrderIndexMessageHandler
{
    /**
     * @param OrderRepositoryContract $orderRepository
     * @param OrderProjector $projector
    */
    public function __construct(
        private OrderRepositoryContract $orderRepository,
        private OrderProjector $projector,
    ) {}

    /**
     * @param OrderIndexMessage $message
     *
     * @return void
    */
    public function __invoke(OrderIndexMessage $message): void
    {
        $order = $this->orderRepository->getOrder($message->orderId);

        if ($order === null) {
            return;
        }

        $this->projector->index($order);
    }
}
